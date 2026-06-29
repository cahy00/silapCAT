<?php
namespace App\Filament\Resources\Documents;
use App\Filament\Resources\Documents\Pages\{CreateDocument, EditDocument, ListDocuments};
use App\Models\Document;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{FileUpload, RichEditor, Select, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{IconColumn, TextColumn};
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $modelLabel = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Dokumen';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Detail Dokumen')->icon('heroicon-o-document')->columnSpan(12)->schema([
                TextInput::make('title')->label('Judul Dokumen')->required()->maxLength(255),
                Select::make('category_id')->relationship('categories', 'name')->required()->label('Kategori'),
                RichEditor::make('desc')->label('Deskripsi (opsional)'),
                FileUpload::make('file')->required()->label('Upload Dokumen')->directory('documents')
                    ->disk('public_uploads')->maxSize(2048)
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                Select::make('is_public')->label('Status')->options([0 => 'Private', 1 => 'Public'])->default(0)->required(),
                TextInput::make('year')->label('Tahun')->required()->numeric()->maxLength(4),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('categories.name')->label('Kategori')->sortable(),
            IconColumn::make('is_public')->boolean(),
            TextColumn::make('year')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
