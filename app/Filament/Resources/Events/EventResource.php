<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid as SchemaGrid;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Tabs as SchemaTabs;
use Filament\Schemas\Components\Tabs\Tab as SchemaTab;
use Filament\Schemas\Components\Group as SchemaGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaGrid::make(12)
                    ->schema([
                        SchemaSection::make('Informasi Utama')
                            ->description('Detail dasar kegiatan seleksi.')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Kegiatan')
                                    ->size(\Filament\Support\Enums\TextSize::Large)
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                    ->icon('heroicon-o-flag'),
                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->placeholder('Tidak ada deskripsi tambahan.')
                                    ->prose(),
                            ])
                            ->columnSpan(8),

                        SchemaSection::make('Status & Klasifikasi')
                            ->description('Status operasional saat ini.')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status Operasional')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'active' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('procurementType.name')
                                    ->label('Jenis Pengadaan')
                                    ->icon('heroicon-o-briefcase')
                                    ->color('info'),
                                TextEntry::make('formation_year')
                                    ->label('Tahun Formasi')
                                    ->icon('heroicon-o-calendar-days'),
                            ])
                            ->columnSpan(4),
                    ])
                    ->columnSpanFull(),

                SchemaTabs::make('Manajemen Detil')
                    ->tabs([
                        SchemaTab::make('Institusi Terlibat')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                RepeatableEntry::make('eventInstitutions')
                                    ->label(null)
                                    ->schema([
                                        SchemaGrid::make(4)->schema([
                                            TextEntry::make('institution.name')
                                                ->label('Institusi')
                                                ->weight('bold')
                                                ->icon('heroicon-o-building-library'),
                                            TextEntry::make('participants_count')
                                                ->label('Kuota Peserta')
                                                ->numeric()
                                                ->icon('heroicon-o-users')
                                                ->suffix(' Peserta'),
                                            TextEntry::make('start_date')
                                                ->label('Mulai Pelaksanaan')
                                                ->date()
                                                ->icon('heroicon-o-play-circle'),
                                            TextEntry::make('end_date')
                                                ->label('Selesai Pelaksanaan')
                                                ->date()
                                                ->icon('heroicon-o-stop-circle'),
                                        ]),
                                    ]),
                            ]),

                        SchemaTab::make('Lokasi Ujian')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                RepeatableEntry::make('eventLocations')
                                    ->label(null)
                                    ->schema([
                                        SchemaGrid::make(3)->schema([
                                            SchemaGroup::make()->schema([
                                                TextEntry::make('location.name')
                                                    ->label('Titik Lokasi')
                                                    ->weight('bold')
                                                    ->icon('heroicon-o-map'),
                                                TextEntry::make('location.city')
                                                    ->label('Kota/Kabupaten')
                                                    ->size(\Filament\Support\Enums\TextSize::Small),
                                            ]),
                                            TextEntry::make('location.address')
                                                ->label('Alamat Lengkap')
                                                ->icon('heroicon-o-home')
                                                ->limit(100),
                                            TextEntry::make('location.type')
                                                ->label('Tipe Lokasi')
                                                ->badge()
                                                ->color('warning')
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'mandiri_bkn' => 'Mandiri BKN',
                                                    'mandiri_instansi' => 'Mandiri Instansi',
                                                    'bkn' => 'BKN',
                                                    default => $state,
                                                }),
                                        ]),
                                        
                                        SchemaSection::make('Hasil Survey Lokasi')
                                            ->compact()
                                            ->schema([
                                                SchemaGrid::make(3)->schema([
                                                    TextEntry::make('location.locationSurvey.feasibility_status')
                                                        ->label('Kelayakan')
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
                                                    TextEntry::make('location.locationSurvey.pc_count')
                                                        ->label('Kapasitas PC')
                                                        ->icon('heroicon-o-computer-desktop')
                                                        ->suffix(' Unit'),
                                                    TextEntry::make('location.locationSurvey.room_count')
                                                        ->label('Jumlah Ruangan')
                                                        ->icon('heroicon-o-home-modern')
                                                        ->suffix(' Ruang'),
                                                ]),
                                            ])
                                            ->visible(fn ($record) => $record->location?->locationSurvey !== null),
                                    ]),
                            ]),

                        SchemaTab::make('Tim Pelaksana')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                RepeatableEntry::make('eventEmployees')
                                    ->label(null)
                                    ->schema([
                                        SchemaGrid::make(3)->schema([
                                            TextEntry::make('employee.name')
                                                ->label('Nama Pegawai')
                                                ->weight('bold')
                                                ->icon('heroicon-o-user'),
                                            TextEntry::make('employee.employee_number')
                                                ->label('NIP/Identitas')
                                                ->copyable()
                                                ->icon('heroicon-o-identification'),
                                            TextEntry::make('role')
                                                ->label('Penugasan')
                                                ->badge()
                                                ->color('success')
                                                ->separator(', '),
                                        ]),
                                    ]),
                            ]),

                        SchemaTab::make('Kelengkapan Dokumen')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                SchemaGrid::make(2)->schema([
                                    SchemaSection::make('Laporan & SK')
                                        ->compact()
                                        ->schema([
                                            TextEntry::make('doc_implementation_report')
                                                ->label('Laporan Pelaksanaan')
                                                ->icon('heroicon-o-document-check')
                                                ->color(fn ($state) => $state ? 'success' : 'gray')
                                                ->formatStateUsing(fn ($state) => $state ? 'Tersedia (Klik untuk Lihat)' : 'Belum Tersedia')
                                                ->url(fn ($record) => $record->doc_implementation_report ? asset('storage/' . $record->doc_implementation_report) : null, true),

                                            TextEntry::make('doc_team_decree')
                                                ->label('SK Tim Pelaksana')
                                                ->icon('heroicon-o-document-check')
                                                ->color(fn ($state) => $state ? 'success' : 'gray')
                                                ->formatStateUsing(fn ($state) => $state ? 'Tersedia (Klik untuk Lihat)' : 'Belum Tersedia')
                                                ->url(fn ($record) => $record->doc_team_decree ? asset('storage/' . $record->doc_team_decree) : null, true),
                                        ]),
                                    
                                    SchemaSection::make('Administrasi Instansi')
                                        ->compact()
                                        ->schema([
                                            TextEntry::make('doc_ba_catos')
                                                ->label('Berita Acara CATOS')
                                                ->icon('heroicon-o-document-check')
                                                ->color(fn ($state) => $state ? 'success' : 'gray')
                                                ->formatStateUsing(fn ($state) => $state ? 'Tersedia (Klik untuk Lihat)' : 'Belum Tersedia')
                                                ->url(fn ($record) => $record->doc_ba_catos ? asset('storage/' . $record->doc_ba_catos) : null, true),

                                            TextEntry::make('doc_institution_announcement')
                                                ->label('Pengumuman Instansi')
                                                ->icon('heroicon-o-document-check')
                                                ->color(fn ($state) => $state ? 'success' : 'gray')
                                                ->formatStateUsing(fn ($state) => $state ? 'Tersedia (Klik untuk Lihat)' : 'Belum Tersedia')
                                                ->url(fn ($record) => $record->doc_institution_announcement ? asset('storage/' . $record->doc_institution_announcement) : null, true),
                                        ]),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['eventInstitutions.institution', 'eventLocations.location']);
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
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
