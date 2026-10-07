<?php

namespace App\Filament\Resources\ProcurementTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProcurementTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('procurement_category_id')
                    ->relationship('procurementCategory', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('passing_grade')
                    ->label('Passing Grade Skor CAT')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->placeholder((string) config('scoring.default_passing_grade'))
                    ->helperText('Kosongkan untuk memakai nilai default ('.config('scoring.default_passing_grade').'). Dipakai pada peta distribusi skor & Executive Summary.'),
            ]);
    }
}
