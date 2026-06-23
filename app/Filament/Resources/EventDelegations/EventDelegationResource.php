<?php

namespace App\Filament\Resources\EventDelegations;

use App\Filament\Resources\EventDelegations\Pages\CreateEventDelegation;
use App\Filament\Resources\EventDelegations\Pages\EditEventDelegation;
use App\Filament\Resources\EventDelegations\Pages\ListEventDelegations;
use App\Filament\Resources\EventDelegations\Schemas\EventDelegationForm;
use App\Filament\Resources\EventDelegations\Tables\EventDelegationsTable;
use App\Models\EventDelegation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventDelegationResource extends Resource
{
    protected static ?string $model = EventDelegation::class;

    protected static ?string $modelLabel = 'Delegasi Tim';
    protected static ?string $pluralModelLabel = 'Delegasi Tim';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static \UnitEnum|string|null $navigationGroup = 'Kegiatan';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return EventDelegationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventDelegationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventDelegations::route('/'),
            'create' => CreateEventDelegation::route('/create'),
            'edit' => EditEventDelegation::route('/{record}/edit'),
        ];
    }
}
