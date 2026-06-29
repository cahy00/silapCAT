<?php
namespace App\Filament\Resources\Announcements;
use App\Filament\Resources\Announcements\Pages\{CreateAnnouncement, EditAnnouncement, ListAnnouncements};
use App\Models\Announcement;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{FileUpload, RichEditor, TextInput, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{IconColumn, ImageColumn, TextColumn};
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Pengumuman';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-speaker-wave';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Detail Pengumuman')->icon('heroicon-o-speaker-wave')->columnSpan(12)->schema([
                TextInput::make('title')->label('Judul Pengumuman')->required()->maxLength(255),
                RichEditor::make('content')->label('Deskripsi')->required()->columnSpanFull(),
                FileUpload::make('file')->label('Foto')->directory('announcements')->disk('public_uploads')
                    ->maxSize(2048)->image()
                    ->helperText('Hanya file gambar (JPG, PNG). Maksimal ukuran 2 MB.')
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png']),
                TextInput::make('link')->label('Link (opsional)')->url(),
                Toggle::make('is_active')->label('Status Aktif')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->limit(20),
            TextColumn::make('content')->html()->limit(20)->searchable(),
            ImageColumn::make('file')->disk('public_uploads'),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
