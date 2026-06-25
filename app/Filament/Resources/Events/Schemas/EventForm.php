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
    public static function hydrateLocationFields($state, callable $set): void
    {
        if ($state) {
            $location = \App\Models\Location::with('locationSurvey')->find($state);
            $set('location_city', $location?->city);
            $set('location_address', $location?->address);
            
            // Survey Data
            $survey = $location?->locationSurvey;
            $set('pc_capacity', $survey?->pc_count ?? 0);
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
            $set('pc_capacity', 0);
            $set('survey_feasibility', null);
            $set('survey_pc_count', null);
            $set('survey_room_count', null);
        }
    }

    public static function updateEventName(callable $set, callable $get): void
    {
        $categoryId = $get('procurement_category_id');
        $typeId = $get('procurement_type_id');
        $year = $get('formation_year');
        
        if ($categoryId && $typeId && $year) {
            $category = \App\Models\ProcurementCategory::find($categoryId)?->name ?? '';
            $type = \App\Models\ProcurementType::find($typeId)?->name ?? '';
            
            // Format: [Kategori] [Jenis] Tahun [Tahun]
            // Contoh: Seleksi CPNS Tahun 2026
            $set('name', trim("$category $type Tahun $year"));
        }
    }

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
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateEventName($set, $get))
                                    ->afterStateHydrated(function (Select $component, $record) {
                                        if ($record && $record->procurementType) {
                                            $component->state($record->procurementType->procurement_category_id);
                                        }
                                    }),
                                Select::make('procurement_type_id')
                                    ->label('Jenis Pengadaan')
                                    ->options(fn (callable $get) => \App\Models\ProcurementType::where('procurement_category_id', $get('procurement_category_id'))->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateEventName($set, $get))
                                    ->visible(fn (callable $get) => filled($get('procurement_category_id'))),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('formation_year')
                                    ->label('Tahun Formasi')
                                    ->numeric()
                                    ->minValue(2000)
                                    ->maxValue(2099)
                                    ->default(date('Y'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateEventName($set, $get)),
                                \Filament\Forms\Components\Placeholder::make('status_display')
                                    ->label('Status Kegiatan')
                                    ->content(function (callable $get) {
                                        $start = $get('start_date');
                                        $end = $get('end_date');
                                        
                                        if (empty($start) || empty($end)) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-white/5 dark:text-gray-400">DRAFT (Belum ada jadwal)</span>');
                                        }
                                        
                                        $today = \Carbon\Carbon::today();
                                        $startDate = \Carbon\Carbon::parse($start);
                                        $endDate = \Carbon\Carbon::parse($end);
                                        
                                        if ($today->between($startDate, $endDate)) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">BERLANGSUNG</span>');
                                        } elseif ($today->gt($endDate)) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">SUDAH SELESAI</span>');
                                        } else {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-white/5 dark:text-gray-400">DRAFT (Belum Mulai)</span>');
                                        }
                                    }),
                                \Filament\Forms\Components\Hidden::make('status'),
                            ]),
                            Grid::make(2)->schema([
                                DatePicker::make('start_date')
                                    ->label('Range Global (Mulai)')
                                    ->required()
                                    ->live(),
                                DatePicker::make('end_date')
                                    ->label('Range Global (Selesai)')
                                    ->required()
                                    ->live()
                                    ->afterOrEqual('start_date'),
                            ]),
                        ]),

                    Step::make('Informasi Utama')
                        ->description('Berikan nama dan deskripsi kegiatan.')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Kegiatan')
                                ->placeholder('Terisi otomatis berdasarkan Kategori, Jenis Pengadaan, dan Tahun')
                                ->required()
                                ->readOnly()
                                ->helperText('Nama kegiatan akan digenerate otomatis agar seragam.')
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
                                    Tab::make('Lokasi & Instansi')
                                        ->icon('heroicon-o-map-pin')
                                        ->schema([
                                            Repeater::make('eventLocations')
                                                ->relationship()
                                                ->defaultItems(0)
                                                ->live()
                                                ->afterStateUpdated(function (callable $set, callable $get) {
                                                    self::syncEmployeeEstimates($set, $get, false);
                                                })
                                                ->schema([
                                                    Section::make('Pilih Titik Lokasi')
                                                        ->compact()
                                                        ->schema([
                                                            Grid::make(3)->schema([
                                                                Select::make('location_id')
                                                                    ->label('Lokasi')
                                                                    ->relationship('location', 'name')
                                                                    ->required()
                                                                    ->live()
                                                                    ->createOptionForm([
                                                                        \Filament\Schemas\Components\Section::make('Identitas Lokasi')
                                                                            ->description('Masukkan detail dasar dari titik lokasi seleksi.')
                                                                            ->icon('heroicon-o-map-pin')
                                                                            ->schema([
                                                                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                                                                    \Filament\Forms\Components\TextInput::make('name')
                                                                                        ->label('Nama Lokasi')
                                                                                        ->placeholder('Contoh: Kantor Regional XIV BKN')
                                                                                        ->required()
                                                                                        ->columnSpan(2),
                                                                                    \Filament\Forms\Components\Select::make('type')
                                                                                        ->label('Jenis Titik Lokasi')
                                                                                        ->options([
                                                                                            'mandiri_bkn' => 'Mandiri BKN',
                                                                                            'mandiri_instansi' => 'Mandiri Instansi',
                                                                                            'bkn' => 'BKN',
                                                                                        ])
                                                                                        ->native(false)
                                                                                        ->default('bkn')
                                                                                        ->required(),
                                                                                    \Filament\Forms\Components\TextInput::make('city')
                                                                                        ->label('Kota/Kabupaten')
                                                                                        ->placeholder('Contoh: Manokwari')
                                                                                        ->required(),
                                                                                    \Filament\Forms\Components\Textarea::make('address')
                                                                                        ->label('Alamat Lengkap')
                                                                                        ->placeholder('Alamat jalan, gedung, dll.')
                                                                                        ->rows(3)
                                                                                        ->columnSpan(2),
                                                                                ]),
                                                                            ]),
                                                                        \Filament\Schemas\Components\Section::make('Data Kelayakan & Fasilitas')
                                                                            ->description('Hasil survey infrastruktur lokasi seleksi.')
                                                                            ->icon('heroicon-o-clipboard-document-check')
                                                                            ->schema([
                                                                                \Filament\Schemas\Components\Grid::make(2)->schema([
                                                                                    \Filament\Forms\Components\TextInput::make('pc_count')
                                                                                        ->label('Jumlah PC Keseluruhan')
                                                                                        ->numeric()
                                                                                        ->required()
                                                                                        ->prefixIcon('heroicon-o-computer-desktop')
                                                                                        ->helperText('Kapasitas dasar untuk sesi.'),
                                                                                    \Filament\Forms\Components\TextInput::make('room_count')
                                                                                        ->label('Jumlah Ruangan')
                                                                                        ->numeric()
                                                                                        ->required()
                                                                                        ->default(1)
                                                                                        ->prefixIcon('heroicon-o-home-modern'),
                                                                                    \Filament\Forms\Components\Select::make('feasibility_status')
                                                                                        ->label('Status Kelayakan')
                                                                                        ->options([
                                                                                            'feasible' => 'Layak',
                                                                                            'not_feasible' => 'Tidak Layak',
                                                                                            'conditional' => 'Bersyarat',
                                                                                        ])
                                                                                        ->native(false)
                                                                                        ->required()
                                                                                        ->default('feasible'),
                                                                                    \Filament\Forms\Components\Select::make('surveyor_name')
                                                                                        ->label('Tim Surveyor')
                                                                                        ->options(\App\Models\Employee::pluck('name', 'name')->toArray())
                                                                                        ->multiple()
                                                                                        ->searchable()
                                                                                        ->prefixIcon('heroicon-o-user-group'),
                                                                                    \Filament\Forms\Components\DatePicker::make('survey_start_date')
                                                                                        ->label('Mulai Survey')
                                                                                        ->prefixIcon('heroicon-o-calendar')
                                                                                        ->required()
                                                                                        ->default(now()),
                                                                                    \Filament\Forms\Components\DatePicker::make('survey_end_date')
                                                                                        ->label('Selesai Survey')
                                                                                        ->prefixIcon('heroicon-o-calendar')
                                                                                        ->required()
                                                                                        ->default(now()),
                                                                                    \Filament\Forms\Components\Textarea::make('notes')
                                                                                        ->label('Catatan Tambahan')
                                                                                        ->rows(2)
                                                                                        ->columnSpan(2),
                                                                                ]),
                                                                            ]),
                                                                    ])
                                                                    ->createOptionAction(function ($action) {
                                                                        return $action
                                                                            ->modalHeading('Buat Titik Lokasi Baru')
                                                                            ->modalWidth('2xl')
                                                                            ->modalDescription('Tambahkan lokasi baru ke dalam sistem untuk digunakan pada berbagai kegiatan seleksi.');
                                                                    })
                                                                    ->createOptionUsing(function (array $data) {
                                                                        $location = \App\Models\Location::create([
                                                                            'name' => $data['name'],
                                                                            'type' => $data['type'],
                                                                            'city' => $data['city'],
                                                                            'address' => $data['address'] ?? null,
                                                                        ]);
                                                                        
                                                                        $location->locationSurvey()->create([
                                                                            'pc_count' => $data['pc_count'],
                                                                            'room_count' => $data['room_count'] ?? 1,
                                                                            'feasibility_status' => $data['feasibility_status'] ?? 'feasible',
                                                                            'surveyor_name' => $data['surveyor_name'] ?? [],
                                                                            'survey_start_date' => $data['survey_start_date'] ?? now(),
                                                                            'survey_end_date' => $data['survey_end_date'] ?? now(),
                                                                            'notes' => $data['notes'] ?? null,
                                                                        ]);
                                                                        
                                                                        return $location->getKey();
                                                                    })
                                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                                        self::hydrateLocationFields($state, $set);
                                                                        self::syncEmployeeEstimates($set, $get, true);
                                                                    })
                                                                    ->afterStateHydrated(function ($state, callable $set) {
                                                                        self::hydrateLocationFields($state, $set);
                                                                    }),
                                                                TextInput::make('location_city')
                                                                    ->label('Kota')
                                                                    ->disabled()
                                                                    ->dehydrated(false),
                                                                TextInput::make('location_address')
                                                                    ->label('Alamat')
                                                                    ->disabled()
                                                                    ->dehydrated(false),
                                                            ]),
                                                        ]),

                                                    Section::make('Rincian Instansi & Peserta')
                                                        ->description('Tentukan instansi mana saja yang melaksanakan seleksi di titik lokasi ini beserta pesertanya.')
                                                        ->icon('heroicon-o-building-office-2')
                                                        ->compact()
                                                        ->schema([
                                                            Repeater::make('eventLocationInstitutions')
                                                                ->relationship()
                                                                ->defaultItems(0)
                                                                ->live()
                                                                ->afterStateUpdated(function (callable $set, callable $get) {
                                                                    self::syncEmployeeEstimates($set, $get, true);
                                                                })
                                                                ->schema([
                                                                    Select::make('institution_id')
                                                                        ->label('Institusi')
                                                                        ->relationship('institution', 'name')
                                                                        ->required()
                                                                        ->live()
                                                                        ->createOptionForm([
                                                                            \Filament\Schemas\Components\Section::make('Profil Instansi')
                                                                                ->description('Masukkan data institusi/kementerian penyelenggara.')
                                                                                ->icon('heroicon-o-building-office-2')
                                                                                ->schema([
                                                                                    \Filament\Schemas\Components\Grid::make(2)->schema([
                                                                                        \Filament\Forms\Components\TextInput::make('name')
                                                                                            ->label('Nama Instansi')
                                                                                            ->placeholder('Contoh: Kementerian Keuangan')
                                                                                            ->required()
                                                                                            ->columnSpan(2),
                                                                                        \Filament\Forms\Components\TextInput::make('code')
                                                                                            ->label('Kode Instansi')
                                                                                            ->placeholder('Misal: KEMENKEU')
                                                                                            ->prefixIcon('heroicon-o-qr-code'),
                                                                                        \Filament\Forms\Components\TextInput::make('email')
                                                                                            ->label('Email Kontak')
                                                                                            ->email()
                                                                                            ->placeholder('email@instansi.go.id')
                                                                                            ->prefixIcon('heroicon-o-envelope'),
                                                                                        \Filament\Forms\Components\TextInput::make('contact_person')
                                                                                            ->label('Contact Person')
                                                                                            ->placeholder('Nama PIC / Penghubung')
                                                                                            ->prefixIcon('heroicon-o-user'),
                                                                                        \Filament\Forms\Components\TextInput::make('phone')
                                                                                            ->label('Nomor Telepon')
                                                                                            ->tel()
                                                                                            ->placeholder('081234567890')
                                                                                            ->prefixIcon('heroicon-o-phone'),
                                                                                        \Filament\Forms\Components\Textarea::make('address')
                                                                                            ->label('Alamat Kantor')
                                                                                            ->placeholder('Alamat lengkap instansi (Opsional)')
                                                                                            ->rows(2)
                                                                                            ->columnSpan(2),
                                                                                    ])
                                                                                ])
                                                                        ])
                                                                        ->createOptionAction(function ($action) {
                                                                            return $action
                                                                                ->modalHeading('Tambah Instansi Baru')
                                                                                ->modalWidth('2xl')
                                                                                ->modalDescription('Tambahkan institusi atau kementerian baru sebagai peserta pengadaan.');
                                                                        })
                                                                        ->createOptionUsing(function (array $data) {
                                                                            $institution = \App\Models\Institution::create([
                                                                                'name' => $data['name'],
                                                                                'code' => $data['code'] ?? null,
                                                                                'contact_person' => $data['contact_person'] ?? null,
                                                                                'phone' => $data['phone'] ?? null,
                                                                                'email' => $data['email'] ?? null,
                                                                                'address' => $data['address'] ?? null,
                                                                            ]);
                                                                            
                                                                            return $institution->getKey();
                                                                        }),
                                                                    TextInput::make('participants_count')
                                                                        ->label('Jumlah Peserta')
                                                                        ->numeric()
                                                                        ->required()
                                                                        ->live(),
                                                                ])
                                                                ->columns(2)
                                                                ->addActionLabel('Tambah Instansi'),
                                                                
                                                            \Filament\Forms\Components\Placeholder::make('total_participants_display')
                                                                ->label('Total Peserta di Lokasi Ini')
                                                                ->content(function (callable $get) {
                                                                    $institutions = $get('eventLocationInstitutions') ?? [];
                                                                    $total = collect($institutions)->sum(fn ($i) => (int) ($i['participants_count'] ?? 0));
                                                                    return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-primary-600'>{$total}</span> <span class='text-sm text-gray-500'>Orang</span>");
                                                                }),
                                                        ]),

                                                    Section::make('Perencanaan & Perhitungan Sesi')
                                                        ->description('Tentukan jadwal untuk menghitung beban sesi.')
                                                        ->icon('heroicon-o-calculator')
                                                        ->compact()
                                                        ->schema([
                                                            Grid::make(3)->schema([
                                                                TextInput::make('pc_capacity')
                                                                    ->label('Kapasitas PC')
                                                                    ->numeric()
                                                                    ->disabled()
                                                                    ->dehydrated(false)
                                                                    ->live(),
                                                                Select::make('session_type')
                                                                    ->label('Pengaturan Sesi')
                                                                    ->options([
                                                                        '4_sessions' => '4 Sesi/Hari (Jumat 2 Sesi)',
                                                                        '3_sessions' => '3 Sesi/Hari (Jumat 2 Sesi)',
                                                                    ])
                                                                    ->default('4_sessions')
                                                                    ->required()
                                                                    ->live(),
                                                                \Filament\Forms\Components\Toggle::make('has_opening_day')
                                                                    ->label('Hari Pertama Pembukaan')
                                                                    ->helperText('Sesi di hari pertama akan berkurang 1')
                                                                    ->inline(false)
                                                                    ->live(),
                                                            ]),
                                                            Grid::make(2)->schema([
                                                                DatePicker::make('start_date')
                                                                    ->label('Mulai')
                                                                    ->required()
                                                                    ->live()
                                                                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                                                        if ($state) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_array($result) && !empty($result['endDateFormatted'])) {
                                                                                $set('end_date', $result['endDateFormatted']);
                                                                                
                                                                                $locationId = $get('location_id');
                                                                                $startDate = $state;
                                                                                $endDate = $result['endDateFormatted'];
                                                                                $eventId = $get('../../id');
                                                                                
                                                                                if ($locationId) {
                                                                                    $conflicts = \App\Models\EventLocation::query()
                                                                                        ->where('location_id', $locationId)
                                                                                        ->when($eventId, fn($q) => $q->where('event_id', '!=', $eventId))
                                                                                        ->where(function ($q) use ($startDate, $endDate) {
                                                                                            $q->where('start_date', '<=', $endDate)
                                                                                              ->where('end_date', '>=', $startDate);
                                                                                        })
                                                                                        ->with('event')
                                                                                        ->get();
                                                                                        
                                                                                    if ($conflicts->isNotEmpty()) {
                                                                                        $eventNames = $conflicts->map(fn($c) => $c->event?->name)->filter()->unique()->implode(', ');
                                                                                        \Filament\Notifications\Notification::make()
                                                                                            ->warning()
                                                                                            ->title('Peringatan: Lokasi Sedang Digunakan')
                                                                                            ->body("Lokasi ini sudah dijadwalkan untuk kegiatan: {$eventNames} pada rentang tanggal yang bersinggungan.")
                                                                                            ->persistent()
                                                                                            ->send();
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }),
                                                                DatePicker::make('end_date')
                                                                    ->label('Selesai')
                                                                    ->required()
                                                                    ->live(),
                                                            ]),
                                                            
                                                            Repeater::make('holiday_dates')
                                                                ->label('Tanggal Libur / Dikecualikan')
                                                                ->defaultItems(0)
                                                                ->simple(
                                                                    DatePicker::make('date')
                                                                        ->label('Pilih Tanggal')
                                                                        ->required()
                                                                        ->live()
                                                                        ->native(false)
                                                                )
                                                                ->addActionLabel('Tambah Hari Libur')
                                                                ->grid(4)
                                                                ->columnSpanFull(),
                                                            
                                                            \Filament\Schemas\Components\Fieldset::make('Hasil Estimasi Operasional')
                                                                ->schema([
                                                                    \Filament\Forms\Components\Placeholder::make('est_total_sessions')
                                                                        ->label('Total Sesi')
                                                                        ->content(function (callable $get) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_string($result)) return $result;
                                                                            return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-primary-600'>{$result['totalSessions']}</span> <span class='text-sm text-gray-500'>Sesi</span>");
                                                                        }),
                                                                    \Filament\Forms\Components\Placeholder::make('est_days_needed')
                                                                        ->label('Hari Kerja')
                                                                        ->content(function (callable $get) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_string($result)) return '-';
                                                                            return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-indigo-600'>{$result['daysNeeded']}</span> <span class='text-sm text-gray-500'>Hari</span>");
                                                                        }),
                                                                    \Filament\Forms\Components\Placeholder::make('est_per_session')
                                                                        ->label('Peserta / Sesi')
                                                                        ->content(function (callable $get) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_string($result)) return '-';
                                                                            $html = "<span class='text-2xl font-bold text-amber-600'>{$result['participantsPerSession']}</span> <span class='text-sm text-gray-500'>Orang</span>";
                                                                            if ($result['lastSessionParticipants'] < $result['participantsPerSession']) {
                                                                                $html .= "<div class='text-xs text-gray-400 mt-1'>Sesi terakhir: {$result['lastSessionParticipants']} orang</div>";
                                                                            }
                                                                            return new \Illuminate\Support\HtmlString($html);
                                                                        }),
                                                                    \Filament\Forms\Components\Placeholder::make('est_status')
                                                                        ->label('Status Jadwal')
                                                                        ->content(function (callable $get) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_string($result)) return '-';
                                                                            
                                                                            if ($result['isMissingDates']) {
                                                                                return new \Illuminate\Support\HtmlString("<span class='text-sm text-gray-500'>Pilih tanggal mulai & selesai.</span>");
                                                                            } elseif ($result['isNotEnoughDays']) {
                                                                                return new \Illuminate\Support\HtmlString("<div class='text-sm text-danger-600 font-semibold'>✗ Jadwal Tidak Mencukupi</div><div class='text-xs text-danger-500 mt-1'>Tersedia {$result['selectedWorkingDays']} hari, butuh {$result['daysNeeded']} hari. Perpanjang hingga {$result['endDateString']}.</div>");
                                                                            } else {
                                                                                return new \Illuminate\Support\HtmlString("<div class='text-sm text-success-600 font-semibold'>✓ Jadwal Memenuhi Syarat</div><div class='text-xs text-success-500 mt-1'>Selesai pada {$result['endDateString']}.</div>");
                                                                            }
                                                                        }),
                                                                ])
                                                                ->columns(4),
                                                            
                                                            Section::make('Rincian Jadwal Harian')
                                                                ->description('Detail distribusi peserta per sesi di setiap hari kerja.')
                                                                ->icon('heroicon-o-table-cells')
                                                                ->compact()
                                                                ->collapsed()
                                                                ->schema([
                                                                    \Filament\Forms\Components\Placeholder::make('est_daily_schedule')
                                                                        ->label('')
                                                                        ->content(function (callable $get) {
                                                                            $result = self::calculateEstimations($get);
                                                                            if (is_string($result) || empty($result['dailySchedule'])) return 'Masukkan jumlah peserta dan pilih lokasi terlebih dahulu.';
                                                                            
                                                                            $capacity = $result['pcCapacity'];
                                                                            $remaining = $result['totalParticipants'];
                                                                            
                                                                            $rows = '';
                                                                            $no = 0;
                                                                            foreach ($result['dailySchedule'] as $day) {
                                                                                $no++;
                                                                                $notes = [];
                                                                                if ($day['isOpening']) $notes[] = '🎤 Pembukaan';
                                                                                if ($day['isFriday']) $notes[] = '🕌 Jumat';
                                                                                $noteStr = !empty($notes) ? implode(', ', $notes) : '-';
                                                                                
                                                                                $sessionDetails = [];
                                                                                for ($i = 1; $i <= $day['sessions']; $i++) {
                                                                                    $peserta = min($capacity, $remaining);
                                                                                    $sessionDetails[] = $peserta;
                                                                                    $remaining -= $peserta;
                                                                                    if ($remaining < 0) $remaining = 0;
                                                                                }
                                                                                $sessionStr = implode(' + ', array_map(fn($p) => "{$p}", $sessionDetails));
                                                                                $totalDay = array_sum($sessionDetails);
                                                                                
                                                                                $bgColor = $no % 2 === 0 ? 'background-color: #f9fafb;' : '';
                                                                                
                                                                                $rows .= "<tr style='border-bottom: 1px solid #e5e7eb; {$bgColor}'>"
                                                                                    . "<td style='padding: 10px 14px; font-size: 13px; color: #6b7280; text-align: center;'>{$no}</td>"
                                                                                    . "<td style='padding: 10px 14px; font-size: 13px; font-weight: 500; color: #374151;'>{$day['date']}</td>"
                                                                                    . "<td style='padding: 10px 14px; text-align: center;'><span style='display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: #eef2ff; color: #4338ca; font-weight: 700; font-size: 12px;'>{$day['sessions']}</span></td>"
                                                                                    . "<td style='padding: 10px 14px; font-size: 13px; color: #4b5563; font-family: monospace;'>{$sessionStr}</td>"
                                                                                    . "<td style='padding: 10px 14px; font-size: 13px; font-weight: 700; color: #1f2937; text-align: center;'>{$totalDay}</td>"
                                                                                    . "<td style='padding: 10px 14px; font-size: 12px; color: #9ca3af;'>{$noteStr}</td>"
                                                                                    . "</tr>";
                                                                            }
                                                                            
                                                                            $totalAllParticipants = $result['totalParticipants'];
                                                                            $totalAllSessions = $result['totalSessions'];
                                                                            
                                                                            $table = "<div style='width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 8px;'>"
                                                                                . "<table style='width: 100%; border-collapse: collapse; min-width: 600px;'>"
                                                                                . "<thead><tr style='background: #f3f4f6; border-bottom: 2px solid #d1d5db;'>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: center; width: 50px;'>No</th>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;'>Hari / Tanggal</th>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;'>Sesi</th>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;'>Peserta per Sesi</th>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;'>Total</th>"
                                                                                . "<th style='padding: 12px 14px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;'>Keterangan</th>"
                                                                                . "</tr></thead>"
                                                                                . "<tbody>{$rows}</tbody>"
                                                                                . "<tfoot><tr style='background: #f3f4f6; border-top: 2px solid #9ca3af;'>"
                                                                                . "<td colspan='2' style='padding: 12px 14px; font-size: 13px; font-weight: 700; color: #374151;'>TOTAL</td>"
                                                                                . "<td style='padding: 12px 14px; font-size: 13px; font-weight: 700; color: #4f46e5; text-align: center;'>{$totalAllSessions}</td>"
                                                                                . "<td style='padding: 12px 14px;'></td>"
                                                                                . "<td style='padding: 12px 14px; font-size: 13px; font-weight: 700; color: #4f46e5; text-align: center;'>{$totalAllParticipants}</td>"
                                                                                . "<td style='padding: 12px 14px;'></td>"
                                                                                . "</tr></tfoot>"
                                                                                . "</table></div>";
                                                                            
                                                                            return new \Illuminate\Support\HtmlString($table);
                                                                        }),
                                                                ]),
                                                        ]),
                                                    
                                                    Section::make('Hasil Survey Lokasi')
                                                        ->description('Informasi teknis berdasarkan survey terakhir.')
                                                        ->compact()
                                                        ->collapsed()
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
                                                ->columns(1)
                                                ->addActionLabel('Tambah Lokasi'),
                                        ]),

                                    Tab::make('Pegawai')
                                        ->icon('heroicon-o-user-group')
                                        ->schema([
                                            Section::make('Estimasi & Realisasi Kebutuhan Pegawai')
                                                ->description('Dihitung otomatis dari data Lokasi & Survei. Pilih pegawai langsung per peran.')
                                                ->icon('heroicon-o-calculator')
                                                ->compact()
                                                ->schema([
                                                    \Filament\Forms\Components\Placeholder::make('est_role_dashboard')
                                                        ->hiddenLabel()
                                                        ->content(function (callable $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                                            $estimates = self::getLocationEstimates($get, $record);

                                                            $roleCounts = [
                                                                'Koordinator' => count($get('employee_koordinator') ?? []),
                                                                'IT' => count($get('employee_it') ?? []),
                                                                'Pengawas' => count($get('employee_pengawas') ?? []),
                                                            ];

                                                            $roleConfig = [
                                                                'Koordinator' => [
                                                                    'estimate' => $estimates['koordinator'],
                                                                    'filled' => $roleCounts['Koordinator'],
                                                                    'icon' => '👤',
                                                                    'color' => '#6366f1',
                                                                    'bgColor' => '#eef2ff',
                                                                    'desc' => '~1 per lokasi',
                                                                ],
                                                                'IT' => [
                                                                    'estimate' => $estimates['it'],
                                                                    'filled' => $roleCounts['IT'],
                                                                    'icon' => '💻',
                                                                    'color' => '#0ea5e9',
                                                                    'bgColor' => '#f0f9ff',
                                                                    'desc' => '~1 per 50 PC',
                                                                ],
                                                                'Pengawas' => [
                                                                    'estimate' => $estimates['pengawas'],
                                                                    'filled' => $roleCounts['Pengawas'],
                                                                    'icon' => '🛡️',
                                                                    'color' => '#10b981',
                                                                    'bgColor' => '#ecfdf5',
                                                                    'desc' => '~1 per ruangan/25 PC',
                                                                ],
                                                            ];

                                                            $totalEstimate = array_sum($estimates);
                                                            $totalFilled = array_sum($roleCounts);

                                                            $cards = '';
                                                            foreach ($roleConfig as $roleName => $cfg) {
                                                                $pct = $cfg['estimate'] > 0 ? min(100, round(($cfg['filled'] / $cfg['estimate']) * 100)) : 0;
                                                                $remaining = max(0, $cfg['estimate'] - $cfg['filled']);
                                                                $statusColor = $cfg['filled'] >= $cfg['estimate']
                                                                    ? ($cfg['filled'] > $cfg['estimate'] ? '#ef4444' : '#10b981')
                                                                    : '#f59e0b';
                                                                $statusText = $cfg['filled'] >= $cfg['estimate']
                                                                    ? ($cfg['filled'] > $cfg['estimate'] ? 'MELEBIHI' : 'TERPENUHI')
                                                                    : "SISA {$remaining}";

                                                                $cards .= "
                                                                <div style='background: {$cfg['bgColor']}; border: 1px solid {$cfg['color']}20; border-radius: 12px; padding: 16px; flex: 1; min-width: 180px;'>
                                                                    <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;'>
                                                                        <div style='font-size: 13px; font-weight: 700; color: {$cfg['color']};'>{$cfg['icon']} {$roleName}</div>
                                                                        <div style='font-size: 10px; font-weight: 800; color: {$statusColor}; background: {$statusColor}15; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.05em;'>{$statusText}</div>
                                                                    </div>
                                                                    <div style='font-size: 28px; font-weight: 800; color: {$cfg['color']}; line-height: 1;'>{$cfg['filled']}<span style='font-size: 14px; color: #9ca3af; font-weight: 500;'>/{$cfg['estimate']}</span></div>
                                                                    <div style='margin-top: 8px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;'>
                                                                        <div style='height: 100%; width: {$pct}%; background: {$cfg['color']}; border-radius: 3px; transition: width 0.3s;'></div>
                                                                    </div>
                                                                    <div style='font-size: 11px; color: #6b7280; margin-top: 6px;'>{$cfg['desc']}</div>
                                                                </div>";
                                                            }

                                                            $totalPct = $totalEstimate > 0 ? min(100, round(($totalFilled / $totalEstimate) * 100)) : 0;

                                                            return new \Illuminate\Support\HtmlString("
                                                                <div style='display: flex; gap: 12px; flex-wrap: wrap;'>{$cards}</div>
                                                                <div style='margin-top: 12px; padding: 10px 14px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;'>
                                                                    <span style='font-size: 12px; font-weight: 600; color: #374151;'>Total Realisasi Tim: {$totalFilled} / {$totalEstimate} orang ({$totalPct}%)</span>
                                                                    <div style='width: 200px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;'>
                                                                        <div style='height: 100%; width: {$totalPct}%; background: #6366f1; border-radius: 3px;'></div>
                                                                    </div>
                                                                </div>
                                                            ");
                                                        }),
                                                ]),
                                            Section::make('👤 Tim Koordinator')
                                                ->description(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => 'Estimasi kebutuhan: ' . self::getLocationEstimates($get, $record)['koordinator'] . ' orang (~1 per lokasi)')
                                                ->compact()
                                                ->schema([
                                                    Select::make('employee_koordinator')
                                                        ->label('Pilih Koordinator')
                                                        ->multiple()
                                                        ->options(function (callable $get) {
                                                            return self::getAvailableEmployeeOptions($get, 'Koordinator');
                                                        })
                                                        ->searchable()
                                                        ->preload()
                                                        ->live()
                                                        ->maxItems(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => self::getLocationEstimates($get, $record)['koordinator'] ?: 99)
                                                        ->helperText('Pilih beberapa pegawai sekaligus sebagai Koordinator.'),
                                                ]),
                                            Section::make('💻 Tim IT')
                                                ->description(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => 'Estimasi kebutuhan: ' . self::getLocationEstimates($get, $record)['it'] . ' orang (~1 per 50 PC)')
                                                ->compact()
                                                ->schema([
                                                    Select::make('employee_it')
                                                        ->label('Pilih Tim IT')
                                                        ->multiple()
                                                        ->options(function (callable $get) {
                                                            return self::getAvailableEmployeeOptions($get, 'IT');
                                                        })
                                                        ->searchable()
                                                        ->preload()
                                                        ->live()
                                                        ->maxItems(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => self::getLocationEstimates($get, $record)['it'] ?: 99)
                                                        ->helperText('Pilih beberapa pegawai sekaligus sebagai Tim IT.'),
                                                ]),
                                            Section::make('🛡️ Tim Pengawas')
                                                ->description(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => 'Estimasi kebutuhan: ' . self::getLocationEstimates($get, $record)['pengawas'] . ' orang (~1 per ruangan/25 PC)')
                                                ->compact()
                                                ->schema([
                                                    Select::make('employee_pengawas')
                                                        ->label('Pilih Pengawas')
                                                        ->multiple()
                                                        ->options(function (callable $get) {
                                                            return self::getAvailableEmployeeOptions($get, 'Pengawas');
                                                        })
                                                        ->searchable()
                                                        ->preload()
                                                        ->live()
                                                        ->maxItems(fn (callable $get, ?\Illuminate\Database\Eloquent\Model $record) => self::getLocationEstimates($get, $record)['pengawas'] ?: 99)
                                                        ->helperText('Pilih beberapa pegawai sekaligus sebagai Pengawas.'),
                                                ]),
                                        ]),
                                    Tab::make('Dokumen')
                                        ->icon('heroicon-o-document-arrow-up')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                FileUpload::make('doc_implementation_report')
                                                    ->label('Laporan Pelaksanaan')
                                                    ->directory('events/documents')
                                                    ->disk('public')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_team_decree')
                                                    ->label('SK Tim Pelaksana')
                                                    ->directory('events/documents')
                                                    ->disk('public')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_ba_catos')
                                                    ->label('BA CATOS')
                                                    ->directory('events/documents')
                                                    ->disk('public')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('doc_institution_announcement')
                                                    ->label('Pengumuman Instansi')
                                                    ->directory('events/documents')
                                                    ->disk('public')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable(false),
                                                FileUpload::make('certificate_template')
                                                    ->label('Template Sertifikat (Latar Belakang)')
                                                    ->directory('events/certificates')
                                                    ->disk('public')
                                                    ->image()
                                                    ->maxSize(5120)
                                                    ->downloadable()
                                                    ->openable()
                                                    ->helperText('Upload desain sertifikat berupa gambar (JPG/PNG). Gambar ini akan menjadi latar belakang sertifikat kelulusan peserta.')
                                                    ->columnSpanFull(),
                                            ]),
                                        ]),
                                ])->columnSpanFull()
                        ]),
                        
                    Step::make('Review & Simpan')
                        ->description('Periksa kembali seluruh data operasional kegiatan.')
                        ->icon('heroicon-o-check-badge')
                        ->schema([

                            // ── Informasi Utama ──
                            \Filament\Schemas\Components\Section::make('Informasi Utama')
                                ->description('Rangkuman identitas dan status kegiatan')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('rev_summary')
                                        ->hiddenLabel()
                                        ->content(function (callable $get) {
                                            $name = e($get('name') ?: '-');
                                            $year = e($get('formation_year') ?: '-');
                                            $statusRaw = $get('status') ?: 'draft';
                                            $startDate = $get('start_date') ? \Carbon\Carbon::parse($get('start_date'))->translatedFormat('d M Y') : '-';
                                            $endDate = $get('end_date') ? \Carbon\Carbon::parse($get('end_date'))->translatedFormat('d M Y') : '-';

                                            $statusColors = [
                                                'active' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'BERLANGSUNG'],
                                                'completed' => ['bg' => '#d1fae5', 'text' => '#047857', 'label' => 'SELESAI'],
                                                'draft' => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'label' => 'DRAFT'],
                                            ];
                                            $sc = $statusColors[$statusRaw] ?? $statusColors['draft'];

                                            $html = "
                                                <div style='display: flex; gap: 12px; flex-wrap: wrap;'>
                                                    <div style='background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; flex: 1; min-width: 200px;'>
                                                        <div style='font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;'>Nama Kegiatan</div>
                                                        <div style='font-size: 14px; font-weight: 700; color: #111827; line-height: 1.4;'>{$name}</div>
                                                    </div>
                                                    <div style='background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; flex: 1; min-width: 150px;'>
                                                        <div style='font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;'>Tahun Formasi</div>
                                                        <div style='font-size: 14px; font-weight: 700; color: #111827;'>{$year}</div>
                                                    </div>
                                                    <div style='background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; flex: 1; min-width: 200px;'>
                                                        <div style='font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;'>Range Global</div>
                                                        <div style='font-size: 14px; font-weight: 600; color: #111827;'>{$startDate} <span style='font-size: 11px; color: #9ca3af; font-weight: 400;'>sampai</span> {$endDate}</div>
                                                    </div>
                                                    <div style='background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; flex: 1; min-width: 150px;'>
                                                        <div style='font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;'>Status</div>
                                                        <span style='display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em; background: {$sc['bg']}; color: {$sc['text']};'>{$sc['label']}</span>
                                                    </div>
                                                </div>";

                                            return new \Illuminate\Support\HtmlString($html);
                                        }),
                                ]),

                            // ── Tim Pelaksana ──
                            \Filament\Schemas\Components\Section::make('Tim Pelaksana')
                                ->description('Personil penugasan SDM per peran')
                                ->icon('heroicon-o-users')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('rev_emp')
                                        ->hiddenLabel()
                                        ->content(function (callable $get) {
                                            $roleFieldMap = [
                                                'Koordinator' => ['field' => 'employee_koordinator', 'icon' => '👤', 'color' => '#6366f1', 'bg' => '#eef2ff', 'badgeText' => '#4338ca'],
                                                'IT' => ['field' => 'employee_it', 'icon' => '💻', 'color' => '#0ea5e9', 'bg' => '#f0f9ff', 'badgeText' => '#0369a1'],
                                                'Pengawas' => ['field' => 'employee_pengawas', 'icon' => '🛡️', 'color' => '#10b981', 'bg' => '#ecfdf5', 'badgeText' => '#047857'],
                                            ];

                                            $allEmpty = true;
                                            $columns = '';

                                            foreach ($roleFieldMap as $roleName => $config) {
                                                $ids = $get($config['field']) ?? [];
                                                $employees = !empty($ids) ? \App\Models\Employee::whereIn('id', $ids)->get() : collect();
                                                $count = $employees->count();
                                                if ($count > 0) $allEmpty = false;

                                                $items = '';
                                                if ($employees->isEmpty()) {
                                                    $items = "<div style='padding: 12px 16px; font-size: 13px; color: #9ca3af; font-style: italic;'>Belum ada</div>";
                                                } else {
                                                    foreach ($employees as $emp) {
                                                        $initial = strtoupper(mb_substr($emp->name, 0, 1));
                                                        $items .= "
                                                            <div style='padding: 10px 16px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;'>
                                                                <div style='width: 32px; height: 32px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #6b7280; flex-shrink: 0;'>{$initial}</div>
                                                                <div style='min-width: 0;'>
                                                                    <div style='font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>" . e($emp->name) . "</div>
                                                                    <div style='font-size: 11px; color: #6b7280; font-family: monospace;'>NIP. {$emp->employee_number}</div>
                                                                </div>
                                                            </div>";
                                                    }
                                                }

                                                $columns .= "
                                                    <div style='flex: 1; min-width: 250px; background: #ffffff; border: 1px solid {$config['color']}30; border-radius: 12px; overflow: hidden;'>
                                                        <div style='padding: 12px 16px; background: {$config['bg']}; border-bottom: 1px solid {$config['color']}20; display: flex; align-items: center; justify-content: space-between;'>
                                                            <div style='display: flex; align-items: center; gap: 8px;'>
                                                                <span style='font-size: 16px;'>{$config['icon']}</span>
                                                                <span style='font-size: 13px; font-weight: 700; color: {$config['color']};'>{$roleName}</span>
                                                            </div>
                                                            <span style='display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; background: #ffffff; color: {$config['badgeText']};'>{$count} orang</span>
                                                        </div>
                                                        <div style='max-height: 240px; overflow-y: auto;'>{$items}</div>
                                                    </div>";
                                            }

                                            if ($allEmpty) {
                                                return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 24px; color: #9ca3af; font-style: italic; font-size: 13px;'>Belum ada pegawai yang ditugaskan.</div>");
                                            }

                                            return new \Illuminate\Support\HtmlString("<div style='display: flex; gap: 16px; flex-wrap: wrap;'>{$columns}</div>");
                                        }),
                                ]),

                            // ── Distribusi Lokasi & Instansi ──
                            \Filament\Schemas\Components\Section::make('Distribusi Lokasi & Instansi')
                                ->description('Rincian titik lokasi, instansi, dan jumlah peserta')
                                ->icon('heroicon-o-map-pin')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('rev_loc')
                                        ->hiddenLabel()
                                        ->content(function (callable $get) {
                                            $locations = collect($get('eventLocations'))->filter(fn($l) => !empty($l['location_id']));
                                            if ($locations->isEmpty()) {
                                                return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 24px; color: #9ca3af; font-style: italic; font-size: 13px;'>Belum ada lokasi yang ditambahkan.</div>");
                                            }

                                            $grandTotal = 0;
                                            $cards = '';

                                            foreach ($locations as $loc) {
                                                $name = e(Location::find($loc['location_id'])?->name ?? 'Unknown');
                                                $start = !empty($loc['start_date']) ? \Carbon\Carbon::parse($loc['start_date'])->translatedFormat('d M Y') : '-';
                                                $end = !empty($loc['end_date']) ? \Carbon\Carbon::parse($loc['end_date'])->translatedFormat('d M Y') : '-';
                                                $institutions = $loc['eventLocationInstitutions'] ?? [];
                                                $locTotal = collect($institutions)->sum(fn ($i) => (int) ($i['participants_count'] ?? 0));
                                                $grandTotal += $locTotal;

                                                // Build institution rows as table
                                                $instRows = '';
                                                if (empty($institutions)) {
                                                    $instRows = "<tr><td colspan='2' style='padding: 12px 14px; font-size: 13px; color: #9ca3af; font-style: italic; text-align: center;'>Belum ada instansi</td></tr>";
                                                } else {
                                                    $no = 1;
                                                    foreach ($institutions as $inst) {
                                                        $instName = e(Institution::find($inst['institution_id'] ?? null)?->name ?? '-');
                                                        $count = (int) ($inst['participants_count'] ?? 0);
                                                        $bgColor = $no % 2 === 0 ? 'background-color: #f9fafb;' : '';
                                                        $instRows .= "
                                                            <tr style='border-bottom: 1px solid #e5e7eb; {$bgColor}'>
                                                                <td style='padding: 10px 14px; font-size: 13px; color: #374151;'>
                                                                    <div style='display: flex; align-items: center; gap: 8px;'>
                                                                        <div style='width: 6px; height: 6px; border-radius: 50%; background: #6366f1; flex-shrink: 0;'></div>
                                                                        <span style='white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;'>{$instName}</span>
                                                                    </div>
                                                                </td>
                                                                <td style='padding: 10px 14px; font-size: 13px; font-weight: 700; color: #111827; text-align: right;'>{$count}</td>
                                                            </tr>";
                                                        $no++;
                                                    }
                                                }

                                                $cards .= "
                                                    <div style='flex: 1; min-width: 320px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;'>
                                                        <div style='padding: 14px 16px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;'>
                                                            <div style='min-width: 0;'>
                                                                <div style='font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>{$name}</div>
                                                                <div style='font-size: 11px; font-weight: 500; color: #6b7280; display: flex; align-items: center; gap: 6px;'>
                                                                    <svg style='width: 14px; height: 14px;' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'></path></svg>
                                                                    {$start} &mdash; {$end}
                                                                </div>
                                                            </div>
                                                            <span style='display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #ecfdf5; color: #047857; letter-spacing: 0.05em; flex-shrink: 0;'>" . number_format($locTotal) . " PAX</span>
                                                        </div>
                                                        <div style='max-height: 250px; overflow-y: auto;'>
                                                            <table style='width: 100%; border-collapse: collapse;'>
                                                                <tbody>{$instRows}</tbody>
                                                            </table>
                                                        </div>
                                                    </div>";
                                            }

                                            // Grand total banner
                                            $grandTotalFormatted = number_format($grandTotal);
                                            $locCount = $locations->count();
                                            $footer = "
                                                <div style='margin-top: 16px; padding: 16px 20px; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;'>
                                                    <div>
                                                        <div style='font-size: 12px; font-weight: 800; color: #4338ca; text-transform: uppercase; letter-spacing: 0.05em;'>Total Keseluruhan</div>
                                                        <div style='font-size: 13px; color: #6b7280; margin-top: 2px;'>{$locCount} Lokasi</div>
                                                    </div>
                                                    <div style='text-align: right;'>
                                                        <span style='font-size: 28px; font-weight: 900; color: #4338ca;'>{$grandTotalFormatted}</span>
                                                        <span style='font-size: 13px; font-weight: 700; color: #6366f1; margin-left: 6px;'>Peserta</span>
                                                    </div>
                                                </div>";

                                            return new \Illuminate\Support\HtmlString("<div style='display: flex; gap: 16px; flex-wrap: wrap;'>{$cards}</div>{$footer}");
                                        }),
                                ]),
                        ]),
                ])
                ->submitAction(new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="md"
                        color="primary"
                        icon="heroicon-o-check-circle"
                    >
                        Simpan Kegiatan
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull()
            ]);
    }

    private static function calculateEstimations(callable $get)
    {
        $institutions = $get('eventLocationInstitutions') ?? [];
        $participants = collect($institutions)->sum(fn ($i) => (int) ($i['participants_count'] ?? 0));
        
        $capacity = (int) $get('pc_capacity');
        $start = $get('start_date');
        $end = $get('end_date');
        $sessionType = $get('session_type') ?? '4_sessions';
        $hasOpeningDay = (bool) $get('has_opening_day');
        $holidayDatesRaw = $get('holiday_dates') ?? [];
        $holidayDates = collect($holidayDatesRaw)
            ->map(function ($d) {
                // Handle both new flat array format and old associative array format for backward compatibility
                $dateString = is_array($d) ? ($d['date'] ?? null) : $d;
                return $dateString ? \Carbon\Carbon::parse($dateString)->format('Y-m-d') : null;
            })
            ->filter()
            ->toArray();

        if ($participants <= 0 || $capacity <= 0) {
            return 'Masukkan jumlah peserta dan pilih lokasi untuk melihat estimasi.';
        }

        $totalSessions = ceil($participants / $capacity);
        $participantsPerSession = $capacity; // max per sesi = kapasitas PC
        $lastSessionParticipants = $participants % $capacity;
        if ($lastSessionParticipants === 0) $lastSessionParticipants = $capacity;
        
        $isSkippedDay = function (\Carbon\Carbon $date) use ($holidayDates) {
            if ($date->isSunday()) return true;
            if (in_array($date->format('Y-m-d'), $holidayDates)) return true;
            return false;
        };

        // Calculate how many working days needed and build daily schedule
        $daysNeeded = 0;
        $remainingSessions = $totalSessions;
        $currentDate = $start ? \Carbon\Carbon::parse($start) : \Carbon\Carbon::now();
        $calculatedEndDate = $currentDate->copy();
        $dailySchedule = [];

        while ($remainingSessions > 0) {
            if ($isSkippedDay($calculatedEndDate)) {
                $calculatedEndDate->addDay();
                continue;
            }
            
            $daysNeeded++;
            $isFriday = $calculatedEndDate->isFriday();
            
            if ($sessionType === '4_sessions') {
                $sessionsToday = $isFriday ? 2 : 4;
            } else {
                $sessionsToday = $isFriday ? 2 : 3;
            }
            
            if ($hasOpeningDay && $daysNeeded === 1) {
                $sessionsToday--;
            }
            
            $actualSessions = min($sessionsToday, $remainingSessions);
            $dailySchedule[] = [
                'day' => $daysNeeded,
                'date' => $calculatedEndDate->translatedFormat('D, d M'),
                'sessions' => $actualSessions,
                'isFriday' => $isFriday,
                'isOpening' => $hasOpeningDay && $daysNeeded === 1,
            ];
            
            $remainingSessions -= $sessionsToday;
            
            if ($remainingSessions > 0) {
                $calculatedEndDate->addDay();
            }
        }

        $selectedWorkingDays = 0;
        if ($start && $end) {
            $endDateObj = \Carbon\Carbon::parse($end);
            $tempDate = \Carbon\Carbon::parse($start);
            
            while ($tempDate->lte($endDateObj)) {
                if (!$isSkippedDay($tempDate)) {
                    $selectedWorkingDays++;
                }
                $tempDate->addDay();
            }
        }

        return [
            'totalSessions' => $totalSessions,
            'daysNeeded' => $daysNeeded,
            'isMissingDates' => !($start && $end),
            'isNotEnoughDays' => ($selectedWorkingDays < $daysNeeded),
            'selectedWorkingDays' => $selectedWorkingDays,
            'endDateString' => $calculatedEndDate->translatedFormat('d M Y'),
            'endDateFormatted' => $calculatedEndDate->format('Y-m-d'),
            'participantsPerSession' => $participantsPerSession,
            'lastSessionParticipants' => $lastSessionParticipants,
            'totalParticipants' => $participants,
            'pcCapacity' => $capacity,
            'dailySchedule' => $dailySchedule,
        ];
    }

    public static function getLocationEstimates(callable $get, ?\Illuminate\Database\Eloquent\Model $record = null): array
    {
        $locations = $get('../../eventLocations') ?? [];
        if (empty($locations) && $record) {
            $locations = $record->eventLocations->toArray();
        }

        $totalKoordinator = 0;
        $totalIT = 0;
        $totalPengawas = 0;

        if ($record && $record->exists) {
            $eventLocations = $record->eventLocations()->with('location.locationSurvey')->get();
            
            foreach ($eventLocations as $el) {
                $location = $el->location;
                if (!$location || !$location->locationSurvey) {
                    $totalKoordinator++; // at minimum 1 koordinator per location
                    continue;
                }
                
                $pcCount = $location->locationSurvey->pc_count ?? 0;
                $roomCount = $location->locationSurvey->room_count ?? 0;
                
                $totalKoordinator += 1;
                $totalIT += (int) ceil($pcCount / 50);
                $totalPengawas += max($roomCount, (int) ceil($pcCount / 25));
            }
        }

        // For create mode: try to read from Livewire form state
        if ($totalKoordinator === 0) {
            // Try various $get paths to find eventLocations across tabs
            $locations = $get('eventLocations')
                ?? $get('../eventLocations')
                ?? $get('../../eventLocations')
                ?? $get('../../../eventLocations')
                ?? $get('../../../../eventLocations')
                ?? [];

            foreach ($locations as $loc) {
                if (empty($loc['location_id'])) continue;
                
                $location = \App\Models\Location::with('locationSurvey')->find($loc['location_id']);
                if (!$location || !$location->locationSurvey) {
                    $totalKoordinator++;
                    continue;
                }
                
                $pcCount = $location->locationSurvey->pc_count ?? 0;
                $roomCount = $location->locationSurvey->room_count ?? 0;
                
                $totalKoordinator += 1;
                $totalIT += (int) ceil($pcCount / 50);
                $totalPengawas += max($roomCount, (int) ceil($pcCount / 25));
            }
        }

        return [
            'koordinator' => $totalKoordinator,
            'it' => $totalIT,
            'pengawas' => $totalPengawas,
        ];
    }

    public static function syncEmployeeEstimates(callable $set, callable $get, bool $isInsideRepeater = false): void
    {
        // No longer auto-syncs repeater items; the new multi-select approach handles this naturally.
    }

    /**
     * Get available employee options for a given role, excluding employees already selected in other roles.
     */
    public static function getAvailableEmployeeOptions(callable $get, string $role): array
    {
        // Collect IDs already selected in other roles to exclude duplicates
        $roleFieldMap = [
            'Koordinator' => 'employee_koordinator',
            'IT' => 'employee_it',
            'Pengawas' => 'employee_pengawas',
        ];

        $excludeIds = [];
        foreach ($roleFieldMap as $roleName => $fieldName) {
            if ($roleName === $role) continue;
            $selected = $get($fieldName) ?? [];
            $excludeIds = array_merge($excludeIds, $selected);
        }
        $excludeIds = array_unique(array_filter($excludeIds));

        $query = Employee::query()
            ->whereJsonContains('status', $role);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($emp) => [
                $emp->id => "{$emp->name} - {$emp->position} (NIP: {$emp->employee_number})",
            ])
            ->toArray();
    }

    /**
     * Save the 3 multi-select fields into the eventEmployees relationship.
     */
    public static function saveEventEmployees(\App\Models\Event $event, array $data): void
    {
        $roleFieldMap = [
            'Koordinator' => 'employee_koordinator',
            'IT' => 'employee_it',
            'Pengawas' => 'employee_pengawas',
        ];

        // Build a map: employee_id => [roles]
        $employeeRoles = [];
        foreach ($roleFieldMap as $roleName => $fieldName) {
            $ids = $data[$fieldName] ?? [];
            foreach ($ids as $id) {
                $employeeRoles[$id][] = $roleName;
            }
        }

        // Delete existing and re-create
        $event->eventEmployees()->delete();

        foreach ($employeeRoles as $employeeId => $roles) {
            $event->eventEmployees()->create([
                'employee_id' => $employeeId,
                'role' => $roles,
            ]);
        }
    }

    /**
     * Load eventEmployees relationship data into the 3 multi-select fields.
     */
    public static function loadEventEmployees(\App\Models\Event $event): array
    {
        $roleFieldMap = [
            'Koordinator' => 'employee_koordinator',
            'IT' => 'employee_it',
            'Pengawas' => 'employee_pengawas',
        ];

        $result = [
            'employee_koordinator' => [],
            'employee_it' => [],
            'employee_pengawas' => [],
        ];

        foreach ($event->eventEmployees as $ee) {
            $roles = is_array($ee->role) ? $ee->role : [];
            foreach ($roles as $role) {
                $fieldName = $roleFieldMap[$role] ?? null;
                if ($fieldName) {
                    $result[$fieldName][] = $ee->employee_id;
                }
            }
        }

        return $result;
    }

    /**
     * Calculate event status based on start and end dates.
     */
    public static function calculateStatus(array $data): string
    {
        $start = $data['start_date'] ?? null;
        $end = $data['end_date'] ?? null;
        
        if (empty($start) || empty($end)) {
            return 'draft';
        }
        
        $today = \Carbon\Carbon::today();
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);
        
        if ($today->between($startDate, $endDate)) {
            return 'active';
        } elseif ($today->gt($endDate)) {
            return 'completed';
        } else {
            return 'draft';
        }
    }
}
