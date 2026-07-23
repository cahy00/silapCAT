<?php

namespace App\Filament\Resources\EventDelegations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Event;
use App\Models\EventLocation;

class EventDelegationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Informasi Delegasi')
                    ->icon('heroicon-o-user-group')
                    ->description('Pilih kegiatan, lokasi, dan user yang akan didelegasikan untuk menginput laporan harian.')
                    ->schema([
                        Select::make('event_id')
                            ->label('Kegiatan')
                            ->options(function () {
                                $events = \App\Models\Event::with('eventLocations.location')
                                    ->where(function($q) {
                                        $q->where(function($sub) {
                                            $sub->whereDate('start_date', '<=', now()->startOfDay())
                                                ->whereDate('end_date', '>=', now()->startOfDay());
                                        })->orWhere(function($sub) {
                                            $sub->whereNull('start_date')->where('status', 'aktif');
                                        });
                                    })
                                    ->get();

                                return $events->mapWithKeys(function ($event) {
                                    $locations = $event->eventLocations->map(fn($loc) => $loc->location?->name)
                                        ->filter()
                                        ->unique()
                                        ->join(', ') ?: '-';

                                    return [$event->id => "{$event->name} - {$locations}"];
                                })->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live(),
                        Select::make('event_location_id')
                            ->label('Lokasi Penugasan')
                            ->required()
                            ->options(function (Get $get) {
                                $eventId = $get('event_id');
                                if (!$eventId) {
                                    return [];
                                }

                                return EventLocation::where('event_id', $eventId)
                                    ->with('location')
                                    ->get()
                                    ->pluck('location.name', 'id')
                                    ->toArray();
                            }),
                        Select::make('user_id')
                            ->label('User (Operator)')
                            ->relationship('user', 'name', fn ($query) => $query->role('operator'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3)->columnSpan(12),
            ]);
    }
}
