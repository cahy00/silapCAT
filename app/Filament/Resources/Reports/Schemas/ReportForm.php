<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Informasi Kegiatan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'name')
                            ->required(),
                    ])->columnSpan(12),
                    
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
