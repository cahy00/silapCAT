<?php
namespace App\Filament\Resources\Categories;
use App\Filament\Resources\Categories\Pages\{CreateCategory, EditCategory, ListCategories};
use App\Models\Category;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use stdClass;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $modelLabel = 'Kategori Postingan';
    protected static ?string $pluralModelLabel = 'Kategori Postingan';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Kategori')->columnSpan(12)->schema([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->live(onBlur: true)
                    ->autocomplete(false)
                    ->afterStateUpdated(fn(callable $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->readOnly()
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->limit(20)->sortable(),
            TextColumn::make('slug'),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
