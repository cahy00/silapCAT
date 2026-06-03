<?php

namespace App\Filament\Resources\LocationSurveys\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class LocationSurveyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Detail Survey')
                    ->description('Pilih lokasi dan informasi surveyor.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('location_id')
                            ->relationship('location', 'name')
                            ->required(),
                        Grid::make(3)->schema([
                            TextInput::make('surveyor_name')
                                ->default(null),
                            DatePicker::make('survey_start_date')
                                ->label('Mulai Survey')
                                ->required(),
                            DatePicker::make('survey_end_date')
                                ->label('Selesai Survey')
                                ->required(),
                        ]),
                    ])->columnSpan(12),
                    
                Section::make('Status Kelayakan & Fasilitas')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('pc_count')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('cctv_count')
                                ->required()
                                ->numeric()
                                ->default(0),
                            Select::make('feasibility_status')
                                ->label('Status Kelayakan')
                                ->options([
                                    'feasible' => 'Layak',
                                    'not_feasible' => 'Tidak Layak',
                                    'conditional' => 'Bersyarat',
                                ])
                                ->default(null),
                        ]),
                        Textarea::make('notes')
                            ->default(null)
                            ->columnSpanFull(),
                    ])->columnSpan(12),
            ]);
    }
}
