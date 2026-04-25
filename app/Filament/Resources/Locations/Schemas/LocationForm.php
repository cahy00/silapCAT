<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Informasi Umum')
                            ->description('Detail dasar lokasi titik ujian.')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->label('Nama Lokasi')
                                        ->placeholder('Contoh: Hotel Grand Sahid')
                                        ->required()
                                        ->columnSpan(2),
                                    Select::make('type')
                                        ->label('Jenis Titik Lokasi')
                                        ->options([
                                            'mandiri_bkn' => 'Mandiri BKN',
                                            'mandiri_instansi' => 'Mandiri Instansi',
                                            'bkn' => 'BKN',
                                        ])
                                        ->native(false)
                                        ->default('bkn')
                                        ->required()
                                        ->columnSpan(1),
                                ]),
                                Grid::make(2)->schema([
                                    TextInput::make('city')
                                        ->label('Kota/Kabupaten')
                                        ->placeholder('Contoh: Jakarta Pusat')
                                        ->prefixIcon('heroicon-o-map')
                                        ->default(null),
                                    Textarea::make('address')
                                        ->label('Alamat Lengkap')
                                        ->placeholder('Jl. Jend. Sudirman No. 86...')
                                        ->rows(3)
                                        ->default(null),
                                ]),
                            ]),

                        Tabs::make('Detail Survey & Dokumentasi')
                            ->tabs([
                                Tabs\Tab::make('Data Survey')
                                    ->label('Hasil Survey')
                                    ->icon('heroicon-o-clipboard-document-check')
                                    ->schema([
                                        Group::make()
                                            ->relationship('locationSurvey')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    Select::make('surveyor_name')
                                                        ->label('Tim Surveyor')
                                                        ->options(\App\Models\Employee::all()->mapWithKeys(function ($employee) {
                                                            $roles = collect($employee->status)->map(fn ($r) => match($r) {
                                                                'coordinator' => 'Koordinator',
                                                                'IT' => 'IT',
                                                                'supervisor' => 'Pengawas',
                                                                default => $r
                                                            })->join(', ');
                                                            return [$employee->name => "{$employee->name} [{$roles}]"];
                                                        }))
                                                        ->multiple()
                                                        ->searchable()
                                                        ->placeholder('Pilih petugas survey')
                                                        ->prefixIcon('heroicon-o-user-group')
                                                        ->default(null),
                                                    DatePicker::make('survey_date')
                                                        ->label('Tanggal Survey')
                                                        ->prefixIcon('heroicon-o-calendar'),
                                                ]),
                                                
                                                \Filament\Schemas\Components\Section::make('Kapasitas Infrastruktur')
                                                    ->description('Jumlah perangkat yang tersedia di lokasi.')
                                                    ->schema([
                                                        Grid::make(2)->schema([
                                                            TextInput::make('pc_count')
                                                                ->label('Jumlah PC Keseluruhan')
                                                                ->numeric()
                                                                ->prefixIcon('heroicon-o-computer-desktop')
                                                                ->suffix('Unit')
                                                                ->required()
                                                                ->default(0),
                                                            TextInput::make('room_count')
                                                                ->label('Jumlah Ruangan Seleksi')
                                                                ->numeric()
                                                                ->prefixIcon('heroicon-o-home-modern')
                                                                ->suffix('Ruang')
                                                                ->required()
                                                                ->default(0),
                                                        ]),
                                                    ])->compact(),

                                                \Filament\Schemas\Components\Section::make('Kelayakan')
                                                    ->schema([
                                                        Select::make('feasibility_status')
                                                            ->label('Status Kelayakan')
                                                            ->options([
                                                                'feasible' => 'Layak',
                                                                'not_feasible' => 'Tidak Layak',
                                                                'conditional' => 'Bersyarat',
                                                            ])
                                                            ->native(false)
                                                            ->required()
                                                            ->default('feasible'),
                                                        Textarea::make('notes')
                                                            ->label('Catatan Tambahan')
                                                            ->placeholder('Berikan catatan jika status tidak layak atau bersyarat...')
                                                            ->rows(3)
                                                            ->default(null),
                                                    ])->compact(),
                                            ]),
                                    ]),
                                Tabs\Tab::make('File & Foto')
                                    ->label('Dokumentasi')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        Group::make()
                                            ->relationship('locationSurvey')
                                            ->schema([
                                                FileUpload::make('survey_document')
                                                    ->label('Dokumen BA Survey')
                                                    ->helperText('Upload Berita Acara atau laporan survey dalam format PDF.')
                                                    ->directory('survey-documents')
                                                    ->preserveFilenames()
                                                    ->openable()
                                                    ->downloadable(),
                                                FileUpload::make('photos')
                                                    ->label('Galeri Foto Lokasi')
                                                    ->helperText('Upload foto-foto kondisi ruangan, PC, dan fasilitas lainnya.')
                                                    ->directory('survey-photos')
                                                    ->multiple()
                                                    ->image()
                                                    ->imageEditor()
                                                    ->reorderable()
                                                    ->appendFiles()
                                                    ->panelLayout('grid'),
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
