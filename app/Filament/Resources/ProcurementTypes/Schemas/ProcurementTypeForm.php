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
            ]);
    }
}
