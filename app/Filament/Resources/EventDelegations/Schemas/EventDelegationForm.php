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
                            ->relationship('event', 'name')
                            ->searchable()
                            ->preload()
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
