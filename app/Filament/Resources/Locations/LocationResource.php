<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\CreateLocation;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Resources\Locations\Pages\ViewLocation;
use App\Filament\Resources\Locations\Schemas\LocationForm;
use App\Filament\Resources\Locations\Tables\LocationsTable;
use App\Models\Location;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid as SchemaGrid;
use Filament\Schemas\Components\Group as SchemaGroup;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Tabs as SchemaTabs;
use Filament\Schemas\Components\Tabs\Tab as SchemaTab;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    
    protected static ?string $modelLabel = 'Lokasi Ujian';
    protected static ?string $pluralModelLabel = 'Lokasi Ujian';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaGrid::make(12)
                    ->schema([
                        SchemaSection::make('Informasi Umum')
                            ->description('Detail dasar lokasi titik ujian.')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                SchemaGrid::make(2)->schema([
                                    TextEntry::make('name')
                                        ->label('Nama Lokasi')
                                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                        ->size(\Filament\Support\Enums\TextSize::Large),
                                    TextEntry::make('type')
                                        ->label('Jenis Titik Lokasi')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'mandiri_bkn' => 'warning',
                                            'mandiri_instansi' => 'success',
                                            'bkn' => 'info',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'mandiri_bkn' => 'Mandiri BKN',
                                            'mandiri_instansi' => 'Mandiri Instansi',
                                            'bkn' => 'BKN',
                                            default => $state,
                                        }),
                                ]),
                                SchemaGrid::make(2)->schema([
                                    TextEntry::make('city')
                                        ->label('Kota/Kabupaten')
                                        ->icon('heroicon-o-map'),
                                    TextEntry::make('address')
                                        ->label('Alamat Lengkap')
                                        ->icon('heroicon-o-home'),
                                ]),
                            ])
                            ->columnSpan(8),

                        SchemaSection::make('Status Survey')
                            ->description('Hasil kelayakan infrastruktur.')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                TextEntry::make('locationSurvey.feasibility_status')
                                    ->label('Status Kelayakan')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'feasible' => 'success',
                                        'not_feasible' => 'danger',
                                        'conditional' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'feasible' => 'LAYAK',
                                        'not_feasible' => 'TIDAK LAYAK',
                                        'conditional' => 'BERSYARAT',
                                        default => 'BELUM SURVEY',
                                    }),
                                TextEntry::make('locationSurvey.survey_start_date')
                                    ->label('Mulai Survey')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('locationSurvey.survey_end_date')
                                    ->label('Selesai Survey')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                            ])
                            ->columnSpan(4),
                    ])
                    ->columnSpanFull(),

                SchemaTabs::make('Detail Infrastruktur & Dokumentasi')
                    ->tabs([
                        SchemaTab::make('Kapasitas & Fasilitas')
                            ->icon('heroicon-o-computer-desktop')
                            ->schema([
                                SchemaGrid::make(2)->schema([
                                    TextEntry::make('locationSurvey.pc_count')
                                        ->label('Jumlah PC Keseluruhan')
                                        ->suffix(' Unit')
                                        ->weight('bold')
                                        ->icon('heroicon-o-computer-desktop'),
                                    TextEntry::make('locationSurvey.room_count')
                                        ->label('Jumlah Ruangan')
                                        ->suffix(' Ruang')
                                        ->weight('bold')
                                        ->icon('heroicon-o-home-modern'),
                                ]),
                                TextEntry::make('locationSurvey.surveyor_name')
                                    ->label('Tim Surveyor')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-user-group')
                                    ->separator(', '),
                                TextEntry::make('locationSurvey.notes')
                                    ->label('Catatan Surveyor')
                                    ->prose()
                                    ->placeholder('Tidak ada catatan.'),
                            ]),

                        SchemaTab::make('Dokumentasi Foto')
                            ->icon('heroicon-o-camera')
                            ->schema([
                                ImageEntry::make('locationSurvey.photos')
                                    ->label('Galeri Kondisi Lokasi')
                                    ->columnSpanFull()
                                    ->imageSize(200)
                                    ->placeholder('Tidak ada foto dokumentasi.'),
                            ]),

                        SchemaTab::make('Dokumen BA')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextEntry::make('locationSurvey.survey_document')
                                    ->label('Berita Acara Survey')
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? 'Download Berita Acara Survey' : 'Dokumen Belum Tersedia')
                                    ->url(fn ($record) => $record->locationSurvey?->survey_document ? asset('storage/' . $record->locationSurvey->survey_document) : null, true),
                            ]),

                        SchemaTab::make('Riwayat Kegiatan')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                RepeatableEntry::make('eventLocations')
                                    ->label('Kegiatan di Lokasi Ini')
                                    ->schema([
                                        SchemaGrid::make(3)->schema([
                                            TextEntry::make('event.name')
                                                ->label('Nama Kegiatan')
                                                ->weight('bold')
                                                ->icon('heroicon-o-flag'),
                                            TextEntry::make('event.status')
                                                ->label('Status')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'draft' => 'gray',
                                                    'active' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger',
                                                    default => 'gray',
                                                }),
                                            TextEntry::make('event.formation_year')
                                                ->label('Tahun Formasi')
                                                ->icon('heroicon-o-calendar'),
                                            TextEntry::make('start_date')
                                                ->label('Mulai')
                                                ->icon('heroicon-o-play')
                                                ->getStateUsing(fn($record) => $record->event->eventInstitutions->min('start_date'))
                                                ->date(),
                                            TextEntry::make('end_date')
                                                ->label('Selesai')
                                                ->icon('heroicon-o-stop')
                                                ->getStateUsing(fn($record) => $record->event->eventInstitutions->max('end_date'))
                                                ->date(),
                                        ]),
                                    ])
                                    ->placeholder('Belum ada kegiatan yang menggunakan lokasi ini.'),
                            ]),
                    ])->columnSpanFull(),
            ]);
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
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'view' => ViewLocation::route('/{record}'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }
}
