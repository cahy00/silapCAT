<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\{CreateBanner, EditBanner, ListBanners};
use App\Models\Banner;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{FileUpload, Radio, Select, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{IconColumn, ImageColumn, TextColumn};
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $modelLabel = 'Banner';
    protected static ?string $pluralModelLabel = 'Banner';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Website';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Detail Banner')->icon('heroicon-o-photo')->columnSpan(12)->schema([
                TextInput::make('name')->label('Judul')->required()->maxLength(255),
                TextInput::make('desc')->label('Keterangan')->required()->maxLength(255),
                Select::make('category')->options([
                    'banner' => 'Banner Utama',
                    'struktur_kanreg' => 'Struktur Kantor Regional',
                    'struktur_pimpinan' => 'Struktur Pimpinan',
                    'agenda' => 'Agenda Kantor Regional',
                ])->required()->label('Kategori'),
                FileUpload::make('file')->required()->directory('banners')->disk('public_uploads')
                    ->maxSize(2048)->image()
                    ->helperText('Hanya file gambar (JPG, PNG). Maksimal ukuran 2 MB.')
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png']),
                Radio::make('is_active')->label('Status')
                    ->options([1 => 'Aktif', 0 => 'Tidak Aktif'])->default(1),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('category')->searchable(),
                ImageColumn::make('file')->disk('public_uploads'),
                IconColumn::make('is_active')->boolean()->label('Status'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
