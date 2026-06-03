<?php

namespace App\Filament\Resources\ProcurementCategories;

use App\Filament\Resources\ProcurementCategories\Pages\CreateProcurementCategory;
use App\Filament\Resources\ProcurementCategories\Pages\EditProcurementCategory;
use App\Filament\Resources\ProcurementCategories\Pages\ListProcurementCategories;
use App\Filament\Resources\ProcurementCategories\Schemas\ProcurementCategoryForm;
use App\Filament\Resources\ProcurementCategories\Tables\ProcurementCategoriesTable;
use App\Models\ProcurementCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProcurementCategoryResource extends Resource
{
    protected static ?string $model = ProcurementCategory::class;
    
    protected static ?string $modelLabel = 'Kategori Pengadaan';
    protected static ?string $pluralModelLabel = 'Kategori Pengadaan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProcurementCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcurementCategoriesTable::configure($table);
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
            'index' => ListProcurementCategories::route('/'),
            'create' => CreateProcurementCategory::route('/create'),
            'edit' => EditProcurementCategory::route('/{record}/edit'),
        ];
    }
}
