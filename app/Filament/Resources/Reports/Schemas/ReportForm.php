<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Unique;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventLocation;
use App\Models\EventDelegation;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $isOperator = $user->hasRole('operator');

        // If operator, get their delegated event IDs
        $delegatedEventIds = [];
        if ($isOperator) {
            $delegatedEventIds = EventDelegation::where('user_id', $user->id)
                ->pluck('event_id')
                ->unique()
                ->toArray();
        }

        return $schema
            ->columns(12)
            ->components([
                Section::make('Informasi Kegiatan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('event_id')
                            ->label('Kegiatan')
                            ->options(function () use ($isOperator, $delegatedEventIds) {
                                $events = Event::with(['eventLocations.location', 'eventLocations.eventLocationInstitutions.institution'])
                                    ->when($isOperator, fn($q) => $q->whereIn('id', $delegatedEventIds))
                                    ->get();

                                return $events->mapWithKeys(function ($event) {
                                    $institutions = $event->eventLocations->flatMap(fn($loc) => $loc->eventLocationInstitutions)
                                        ->map(fn($ei) => $ei->institution?->name)
                                        ->filter()
                                        ->unique()
                                        ->join(', ') ?: '-';

                                    $locations = $event->eventLocations->map(fn($loc) => $loc->location?->name)
                                        ->filter()
                                        ->unique()
                                        ->join(', ') ?: '-';

                                    return [$event->id => "{$event->name} - {$institutions} - {$locations}"];
                                })->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) use ($isOperator, $user) {
                                if (!$state) {
                                    $set('event_location_id', null);
                                    return;
                                }

                                $locationId = null;
                                if ($isOperator) {
                                    $locationId = EventDelegation::where('user_id', $user->id)
                                        ->where('event_id', $state)
                                        ->value('event_location_id');
                                }

                                if (!$locationId) {
                                    $locationId = EventLocation::where('event_id', $state)->value('id');
                                }

                                $set('event_location_id', $locationId);
                            })
                            ->columnSpan(fn (string $operation) => $operation === 'create' ? 8 : 4),

                        Hidden::make('event_location_id')
                            ->visibleOn('create')
                            ->default(function (Get $get) use ($isOperator, $user) {
                                $eventId = $get('event_id');
                                if (!$eventId) {
                                    return null;
                                }

                                if ($isOperator) {
                                    $locId = EventDelegation::where('user_id', $user->id)
                                        ->where('event_id', $eventId)
                                        ->value('event_location_id');
                                    if ($locId) {
                                        return $locId;
                                    }
                                }

                                return EventLocation::where('event_id', $eventId)->value('id');
                            })
                            ->dehydrated(),

                        Select::make('event_location_id')
                            ->label('Lokasi')
                            ->required()
                            ->hiddenOn('create')
                            ->options(function (Get $get) use ($isOperator, $user) {
                                $eventId = $get('event_id');
                                if (!$eventId) {
                                    return [];
                                }

                                $query = EventLocation::where('event_id', $eventId)->with('location');

                                if ($isOperator) {
                                    $delegatedLocationIds = EventDelegation::where('user_id', $user->id)
                                        ->where('event_id', $eventId)
                                        ->pluck('event_location_id')
                                        ->toArray();
                                    $query->whereIn('id', $delegatedLocationIds);
                                }

                                return $query->get()->pluck('location.name', 'id')->toArray();
                            })
                            ->columnSpan(3),

                        Select::make('report_date')
                            ->label('Tanggal Laporan')
                            ->required()
                            ->options(function (Get $get) {
                                $eventId = $get('event_id');
                                if (!$eventId) {
                                    return [];
                                }

                                $event = Event::find($eventId);
                                if (!$event || !$event->start_date || !$event->end_date) {
                                    return [];
                                }

                                $options = [];
                                $current = Carbon::parse($event->start_date);
                                $end = Carbon::parse($event->end_date);

                                while ($current->lte($end)) {
                                    $options[$current->format('Y-m-d')] = $current->isoFormat('DD MMMM YYYY');
                                    $current->addDay();
                                }

                                return $options;
                            })
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('event_id', $get('event_id'))
                                    ->where('event_location_id', $get('event_location_id'))
                                    ->where('session_name', $get('session_name') ?? 'N/A');
                            })
                            ->columnSpan(fn (string $operation) => $operation === 'create' ? 4 : 3),

                        Select::make('session_name')
                            ->label('Sesi')
                            ->options([
                                'Sesi 1' => 'Sesi 1',
                                'Sesi 2' => 'Sesi 2',
                                'Sesi 3' => 'Sesi 3',
                                'Sesi 4' => 'Sesi 4',
                            ])
                            ->required()
                            ->hiddenOn('create')
                            ->columnSpan(2),
                    ])->columns(12)->columnSpan(12),
                    
                Section::make('Data Kehadiran')
                    ->icon('heroicon-o-users')
                    ->hiddenOn('create')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('total_participants')
                                ->label('Total Peserta')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->readOnly()
                                ->dehydrated(),
                            TextInput::make('present_count')
                                ->label('Jumlah Hadir')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->live()
                                ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotalParticipants($get, $set)),
                            TextInput::make('absent_count')
                                ->label('Jumlah Tidak Hadir')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->live()
                                ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotalParticipants($get, $set)),
                        ]),
                    ])->columnSpan(12),
                    
                Section::make('Hasil / Nilai')
                    ->icon('heroicon-o-chart-bar')
                    ->hiddenOn('create')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('highest_score')
                                ->label('Nilai Tertinggi')
                                ->numeric()
                                ->default(null),
                            TextInput::make('lowest_score')
                                ->label('Nilai Terendah')
                                ->numeric()
                                ->default(null),
                        ]),
                    ])->columnSpan(12),

                Section::make('Data Sesi')
                    ->icon('heroicon-o-document-duplicate')
                    ->visibleOn('create')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('sessions_repeater')
                            ->label('')
                            ->schema([
                                Select::make('session_name')
                                    ->label('Sesi')
                                    ->options([
                                        'Sesi 1' => 'Sesi 1',
                                        'Sesi 2' => 'Sesi 2',
                                        'Sesi 3' => 'Sesi 3',
                                        'Sesi 4' => 'Sesi 4',
                                    ])
                                    ->required(),
                                
                                TextInput::make('present_count')
                                    ->label('Hadir')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotalParticipants($get, $set)),
                                TextInput::make('absent_count')
                                    ->label('Tidak Hadir')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (callable $get, callable $set) => self::updateTotalParticipants($get, $set)),
                                TextInput::make('total_participants')
                                    ->label('Total Peserta')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->dehydrated(),    
                                TextInput::make('highest_score')
                                    ->label('Tertinggi')
                                    ->numeric()
                                    ->default(null),
                                TextInput::make('lowest_score')
                                    ->label('Terendah')
                                    ->numeric()
                                    ->default(null),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Sesi')
                    ])->columnSpan(12),
            ]);
    }

    public static function updateTotalParticipants(callable $get, callable $set): void
    {
        $present = (int) ($get('present_count') ?? 0);
        $absent = (int) ($get('absent_count') ?? 0);
        $set('total_participants', $present + $absent);
    }
}
