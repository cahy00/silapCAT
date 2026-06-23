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
                                if ($isOperator) {
                                    return Event::whereIn('id', $delegatedEventIds)->pluck('name', 'id');
                                }
                                return Event::pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live(),
                        Select::make('event_location_id')
                            ->label('Lokasi')
                            ->required()
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
                            }),
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
                                    ->where('session_name', $get('session_name'));
                            }),
                        Select::make('session_name')
                            ->label('Sesi')
                            ->options([
                                'Sesi 1' => 'Sesi 1',
                                'Sesi 2' => 'Sesi 2',
                                'Sesi 3' => 'Sesi 3',
                                'Sesi 4' => 'Sesi 4',
                            ])
                            ->required(),
                    ])->columns(4)->columnSpan(12),
                    
                Section::make('Data Kehadiran')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('total_participants')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('present_count')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('absent_count')
                                ->required()
                                ->numeric()
                                ->default(0),
                        ]),
                    ])->columnSpan(12),
                    
                Section::make('Hasil / Nilai')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('highest_score')
                                ->numeric()
                                ->default(null),
                            TextInput::make('lowest_score')
                                ->numeric()
                                ->default(null),
                        ]),
                    ])->columnSpan(12),
            ]);
    }
}
