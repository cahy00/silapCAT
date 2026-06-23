<?php
namespace App\Filament\Resources\Faqs;
use App\Filament\Resources\Faqs\Pages\{CreateFaq, EditFaq, ListFaqs};
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{RichEditor, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $modelLabel = 'FAQ';
    protected static ?string $pluralModelLabel = 'FAQ';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('FAQ')->columnSpan(12)->schema([
                TextInput::make('question')->label('Pertanyaan')->required()->columnSpanFull(),
                RichEditor::make('answer')->label('Jawaban')->required()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question')->label('Pertanyaan')->searchable()->limit(40),
            TextColumn::make('answer')->label('Jawaban')->html()->limit(40)->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
