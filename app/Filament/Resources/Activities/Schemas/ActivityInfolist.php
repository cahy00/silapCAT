<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Aktivitas')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('causer.name')
                            ->label('Pengguna'),
                        \Filament\Infolists\Components\TextEntry::make('log_name')
                            ->label('Kategori')
                            ->badge(),
                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Aktivitas'),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime(),
                    ])->columns(2),
                \Filament\Schemas\Components\Section::make('Data Tambahan')
                    ->schema([
                        \Filament\Infolists\Components\KeyValueEntry::make('properties.attributes')
                            ->label('Data Baru'),
                        \Filament\Infolists\Components\KeyValueEntry::make('properties.old')
                            ->label('Data Lama'),
                    ])->columns(2),
            ]);
    }
}
