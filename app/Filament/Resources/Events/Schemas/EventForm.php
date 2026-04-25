<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Employee;
use App\Models\Institution;
use App\Models\Location;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Wizard::make([
                    Step::make('Jenis Pengadaan & Tahun')
                        ->description('Pilih kategori, jenis pengadaan, dan tahun formasi.')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('procurement_category_id')
                                    ->label('Kategori Pengadaan')
                                    ->options(\App\Models\ProcurementCategory::all()->pluck('name', 'id'))
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Select $component, $record) {
                                        if ($record && $record->procurementType) {
                                            $component->state($record->procurementType->procurement_category_id);
                                        }
                                    }),
                                Select::make('procurement_type_id')
                                    ->label('Jenis Pengadaan')
                                    ->options(fn (callable $get) => \App\Models\ProcurementType::where('procurement_category_id', $get('procurement_category_id'))->pluck('name', 'id'))
                                    ->required()
                                    ->visible(fn (callable $get) => filled($get('procurement_category_id'))),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('formation_year')
                                    ->label('Tahun Formasi')
                                    ->numeric()
                                    ->minValue(2000)
                                    ->maxValue(2099)
                                    ->default(date('Y'))
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'active' => 'Active',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                        ]),

                    Step::make('Informasi Utama')
                        ->description('Berikan nama dan deskripsi kegiatan.')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Kegiatan')
                                ->placeholder('Contoh: Seleksi CPNS 2026')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->placeholder('Berikan ringkasan kegiatan...')
                                ->rows(5)
                                ->default(null),
                        ]),

                    Step::make('Relasi & Dokumen')
                        ->description('Kelola institusi, lokasi, pegawai, dan unggah dokumen.')
                        ->icon('heroicon-o-link')
                        ->schema([
                            Tabs::make('Relasi Event')
                                ->tabs([
                                    Tab::make('Institusi')
                                        ->icon('heroicon-o-building-office-2')
                                        ->schema([
                                            Repeater::make('eventInstitutions')
                                                ->relationship()
                                                ->schema([
                                                    Select::make('institution_id')
                                                        ->label('Institusi')
                                                        ->relationship('institution', 'name')
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, callable $set) {
                                                            if ($state) {
                                                                $institution = Institution::find($state);
                                                                $set('institution_code', $institution?->code);
                                                            } else {
                                                                $set('institution_code', null);
                                                            }
                                                        }),
                                                    TextInput::make('institution_code')
                                                        ->label('Kode')
                                                        ->disabled()
                                                        ->dehydrated(false),
                                                    TextInput::make('participants_count')
                                                        ->label('Peserta')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->default(0)
                                                        ->required(),
                                                    DatePicker::make('start_date')
                                                        ->label('Mulai'),
                                                    DatePicker::make('end_date')
                                                        ->label('Selesai'),
                                                ])
                                                ->columns(5)
                                                ->addActionLabel('Tambah Institusi'),
                                        ]),

                                    Tab::make('Lokasi')
                                        ->icon('heroicon-o-map-pin')
                                        ->schema([
                                            Repeater::make('eventLocations')
                                                ->relationship()
                                                ->schema([
                                                    Select::make('location_id')
                                                        ->label('Lokasi')
                                                        ->relationship('location', 'name')
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, callable $set) {
                                                            if ($state) {
                                                                $location = Location::with('locationSurvey')->find($state);
                                                                $set('location_city', $location?->city);
                                                                $set('location_address', $location?->address);
                                                                
                                                                // Survey Data
                                                                $survey = $location?->locationSurvey;
                                                                $set('survey_feasibility', match($survey?->feasibility_status) {
                                                                    'feasible' => 'LAYAK',
                                                                    'not_feasible' => 'TIDAK LAYAK',
                                                                    'conditional' => 'BERSYARAT',
                                                                    default => 'BELUM SURVEY'
                                                                });
                                                                $set('survey_pc_count', ($survey?->pc_count ?? 0) . ' Unit');
                                                                $set('survey_room_count', ($survey?->room_count ?? 0) . ' Ruang');
                                                            } else {
                                                                $set('location_city', null);
                                                                $set('location_address', null);
                                                                $set('survey_feasibility', null);
                                                                $set('survey_pc_count', null);
                                                                $set('survey_room_count', null);
                                                            }
                                                        }),
                                                    TextInput::make('location_city')
                                                        ->label('Kota')
                                                        ->disabled()
                                                        ->dehydrated(false),
                                                    TextInput::make('location_address')
                                                        ->label('Alamat')
                                                        ->disabled()
                                                        ->dehydrated(false),
                                                    
                                                    Section::make('Hasil Survey Lokasi')
                                                        ->description('Informasi teknis berdasarkan survey terakhir.')
                                                        ->compact()
                                                        ->schema([
                                                            Grid::make(3)->schema([
                                                                TextInput::make('survey_feasibility')
                                                                    ->label('Status Kelayakan')
                                                                    ->disabled()
                                                                    ->dehydrated(false),
                                                                TextInput::make('survey_pc_count')
                                                                    ->label('Kapasitas PC')
                                                                    ->disabled()
                                                                    ->dehydrated(false),
                                                                TextInput::make('survey_room_count')
                                                                    ->label('Jumlah Ruangan')
                                                                    ->disabled()
                                                                    ->dehydrated(false),
                                                            ]),
                                                        ])
                                                        ->visible(fn (callable $get) => filled($get('location_id')))
                                                        ->columnSpanFull(),
                                                ])
                                                ->columns(3)
                                                ->addActionLabel('Tambah Lokasi'),
                                        ]),

                                    Tab::make('Pegawai')
                                        ->icon('heroicon-o-user-group')
                                        ->schema([
                                            Repeater::make('eventEmployees')
                                                ->relationship()
                                                ->schema([
                                                    CheckboxList::make('role')
                                                        ->label('Peran')
                                                        ->options([
                                                            'coordinator' => 'Koordinator',
                                                            'IT' => 'IT',
                                                            'supervisor' => 'Pengawas',
                                                        ])
                                                        ->required()
                                                        ->columns(3),
                                                    Select::make('employee_id')
                                                        ->label('Pegawai')
                                                        ->relationship('employee', 'name')
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, callable $set) {
                                                            if ($state) {
                                                                $employee = Employee::find($state);
                                                                $set('employee_number', $employee?->employee_number);
                                                                $set('employee_position', $employee?->position);
                                                            } else {
                                                                $set('employee_number', null);
                                                                $set('employee_position', null);
                                                            }
                                                        }),
                                                    TextInput::make('employee_number')
                                                        ->label('NIP')
                                                        ->disabled()
                                                        ->dehydrated(false),
                                                    TextInput::make('employee_position')
                                                        ->label('Jabatan')
                                                        ->disabled()
                                                        ->dehydrated(false),
                                                ])
                                                ->columns(1)
                                                ->addActionLabel('Tambah Pegawai'),
                                        ]),
                                    Tab::make('Dokumen')
                                        ->icon('heroicon-o-document-arrow-up')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                FileUpload::make('doc_implementation_report')
                                                    ->label('Laporan Pelaksanaan')
                                                    ->directory('events/documents')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_team_decree')
                                                    ->label('SK Tim Pelaksana')
                                                    ->directory('events/documents')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_ba_catos')
                                                    ->label('BA CATOS')
                                                    ->directory('events/documents')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_institution_announcement')
                                                    ->label('Pengumuman Instansi')
                                                    ->directory('events/documents')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                            ]),
                                        ]),
                                ])->columnSpanFull()
                        ]),
                ])->columnSpanFull()
            ]);
    }
}
