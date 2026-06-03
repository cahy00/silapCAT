<?php

namespace App\Filament\Resources\LocationSurveys;

use App\Filament\Resources\LocationSurveys\Pages\CreateLocationSurvey;
use App\Filament\Resources\LocationSurveys\Pages\EditLocationSurvey;
use App\Filament\Resources\LocationSurveys\Pages\ListLocationSurveys;
use App\Filament\Resources\LocationSurveys\Schemas\LocationSurveyForm;
use App\Filament\Resources\LocationSurveys\Tables\LocationSurveysTable;
use App\Models\LocationSurvey;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LocationSurveyResource extends Resource
{
    protected static ?string $model = LocationSurvey::class;
    
    protected static ?string $modelLabel = 'Survey Lokasi';
    protected static ?string $pluralModelLabel = 'Survey Lokasi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Kegiatan';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return LocationSurveyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationSurveysTable::configure($table);
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
            'index' => ListLocationSurveys::route('/'),
            'create' => CreateLocationSurvey::route('/create'),
            'edit' => EditLocationSurvey::route('/{record}/edit'),
        ];
    }
}
