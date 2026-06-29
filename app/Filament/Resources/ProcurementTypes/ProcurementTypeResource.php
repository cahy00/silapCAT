<?php

namespace App\Filament\Resources\ProcurementTypes;

use App\Filament\Resources\ProcurementTypes\Pages\CreateProcurementType;
use App\Filament\Resources\ProcurementTypes\Pages\EditProcurementType;
use App\Filament\Resources\ProcurementTypes\Pages\ListProcurementTypes;
use App\Filament\Resources\ProcurementTypes\Schemas\ProcurementTypeForm;
use App\Filament\Resources\ProcurementTypes\Tables\ProcurementTypesTable;
use App\Models\ProcurementType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProcurementTypeResource extends Resource
{
    protected static ?string $model = ProcurementType::class;
    
    protected static ?string $modelLabel = 'Jenis Pengadaan';
    protected static ?string $pluralModelLabel = 'Jenis Pengadaan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProcurementTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcurementTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcurementTypes::route('/'),
            'create' => CreateProcurementType::route('/create'),
            'edit' => EditProcurementType::route('/{record}/edit'),
        ];
    }
}
