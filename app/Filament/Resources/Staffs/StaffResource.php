<?php
namespace App\Filament\Resources\Staffs;
use App\Filament\Resources\Staffs\Pages\{CreateStaff, EditStaff, ListStaffs};
use App\Models\Staff;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{FileUpload, Select, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Table;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;
    protected static ?string $modelLabel = 'Pejabat/Pegawai';
    protected static ?string $pluralModelLabel = 'Pejabat/Pegawai';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 8;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Data Pejabat/Pegawai')->icon('heroicon-o-user-circle')->columnSpan(12)->schema([
                TextInput::make('name')->label('Nama Pegawai')->required()->maxLength(255),
                TextInput::make('nip')->label('NIP')->required()->maxLength(255),
                Select::make('departement_id')->relationship('departement', 'name')->required()->label('Unit Kerja'),
                TextInput::make('position')->label('Jabatan')->required()->maxLength(255),
                Select::make('category')->options([
                    'kepala_bkn' => 'Kepala BKN',
                    'jptm' => 'JPT Madya',
                    'kepala_regional' => 'Kepala Kantor Regional XIV BKN',
                    'administrator' => 'Pejabat Administrator',
                    'pengawas' => 'Pejabat Pengawas',
                    'fungsional' => 'Fungsional',
                ])->required()->label('Kategori'),
                FileUpload::make('photo')->label('Foto')->directory('staff')->disk('public_uploads')
                    ->maxSize(2048)->image()
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png']),
                FileUpload::make('lhkpn')->label('Dokumen LHKPN')->directory('lhkpn')->disk('public_uploads')
                    ->maxSize(6000)
                    ->acceptedFileTypes(['application/pdf']),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('departement.name')->label('Unit Kerja')->sortable(),
            TextColumn::make('position')->searchable(),
            TextColumn::make('nip')->searchable(),
            TextColumn::make('category')->searchable(),
            ImageColumn::make('photo')->disk('public_uploads'),
        ])
        ->recordActions([EditAction::make()])
        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffs::route('/'),
            'create' => CreateStaff::route('/create'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }
}
