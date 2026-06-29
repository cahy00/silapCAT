<?php
namespace App\Filament\Resources\Services;
use App\Filament\Resources\Services\Pages\{CreateService, EditService, ListServices};
use App\Models\Service;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{DatePicker, FileUpload, RichEditor, Select, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $modelLabel = 'Layanan';
    protected static ?string $pluralModelLabel = 'Layanan';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Detail Layanan')->icon('heroicon-o-rectangle-stack')->columnSpan(12)->schema([
                TextInput::make('title')->label('Judul')->required()->maxLength(255),
                RichEditor::make('description')->label('Deskripsi')->columnSpanFull(),
                Select::make('category')->options([
                    'cat' => 'Penggunaan CAT', 'cltn' => 'CLTN', 'kp' => 'Kenaikan Pangkat',
                    'mt' => 'Manajemen Talenta', 'mutasi' => 'Mutasi', 'nip' => 'Penetapan NIP & PPK',
                    'pensiun' => 'Pensiun', 'pengangkatan' => 'Pengangkatan C1', 'pg' => 'Penyesuaian Gelar',
                    'peremajaan' => 'Peremajaan', 'pmk' => 'PMK', 'statistik' => 'Statistik ASN',
                    'janda_duda' => 'Pensiun Janda/Duda', 'pembinaan' => 'Pembinaan', 'pengaktifan' => 'Pengaktifan PNS',
                ])->required(),
                DatePicker::make('periode')->label('Periode')->required()->native(false),
                FileUpload::make('thumbnail')->label('Foto Progress')->directory('service_thumbnails')
                    ->disk('public_uploads')->maxSize(2048)->image()
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png']),
                TextInput::make('link')->label('Link (opsional)')->url(),
                FileUpload::make('document')->label('Dokumen Progress')->directory('service_documents')
                    ->disk('public_uploads')->maxSize(10000)
                    ->acceptedFileTypes(['application/pdf']),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->words(5),
            TextColumn::make('category'),
            ImageColumn::make('thumbnail')->disk('public_uploads')->square(),
            TextColumn::make('periode')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
