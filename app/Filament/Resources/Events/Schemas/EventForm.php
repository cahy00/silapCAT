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
                                        $locations = collect($get('eventLocations'))->filter(fn($l) => !empty($l['start_date']) && !empty($l['end_date']));
                                        
                                        if ($locations->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-white/5 dark:text-gray-400">DRAFT (Belum ada jadwal)</span>');
                                        }
                                        
                                        $today = \Carbon\Carbon::today();
                                        $earliestStart = null;
                                        $latestEnd = null;
                                        
                                        foreach ($locations as $loc) {
                                            $start = \Carbon\Carbon::parse($loc['start_date']);
                                            $end = \Carbon\Carbon::parse($loc['end_date']);
                                            
                                            if ($earliestStart === null || $start->lt($earliestStart)) {
                                                $earliestStart = $start;
                                            }
                                            if ($latestEnd === null || $end->gt($latestEnd)) {
                                                $latestEnd = $end;
                                            }
                                        }
                                        
                                        if ($today->between($earliestStart, $latestEnd)) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">BERLANGSUNG</span>');
                                        } elseif ($today->gt($latestEnd)) {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">SUDAH SELESAI</span>');
                                        } else {
                                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-white/5 dark:text-gray-400">DRAFT (Belum Mulai)</span>');
                                        }
                                    }),
                                \Filament\Forms\Components\Hidden::make('status')
                                    ->dehydrateStateUsing(function (callable $get) {
                                        $locations = collect($get('eventLocations'))->filter(fn($l) => !empty($l['start_date']) && !empty($l['end_date']));
                                        
                                        if ($locations->isEmpty()) {
                                            return 'draft';
                                        }
                                        
                                        $today = \Carbon\Carbon::today();
                                        $earliestStart = null;
                                        $latestEnd = null;
                                        
                                        foreach ($locations as $loc) {
                                            $start = \Carbon\Carbon::parse($loc['start_date']);
                                            $end = \Carbon\Carbon::parse($loc['end_date']);
                                            
                                            if ($earliestStart === null || $start->lt($earliestStart)) {
                                                $earliestStart = $start;
                                            }
                                            if ($latestEnd === null || $end->gt($latestEnd)) {
                                                $latestEnd = $end;
                                            }
                                        }
                                        
                                        if ($today->between($earliestStart, $latestEnd)) {
                                            return 'active';
                                        } elseif ($today->gt($latestEnd)) {
                                            return 'completed';
                                        } else {
                                            return 'draft';
                                        }
                                    }),
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
                                                ])
                                                ->columns(2)
                                                ->addActionLabel('Tambah Institusi'),
                                        ]),

                                    Tab::make('Lokasi')
                                        ->icon('heroicon-o-map-pin')
                                        ->schema([
                                            Repeater::make('eventLocations')
                                                ->relationship()
                                                ->live()
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
                                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                                        if ($state) {
                                                                            $location = Location::with('locationSurvey')->find($state);
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

                                                    Section::make('Perencanaan & Perhitungan Sesi')
                                                        ->description('Tentukan kuota dan jadwal untuk menghitung beban sesi.')
                                                        ->icon('heroicon-o-calculator')
                                                        ->compact()
                                                        ->schema([
                                                            Grid::make(4)->schema([
                                                                TextInput::make('participants_count')
                                                                    ->label('Jumlah Peserta')
                                                                    ->numeric()
                                                                    ->default(0)
                                                                    ->required()
                                                                    ->live(),
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
                                                                    ->live(),
                                                                DatePicker::make('end_date')
                                                                    ->label('Selesai')
                                                                    ->required()
                                                                    ->live(),
                                                            ]),
                                                            
                                                            Repeater::make('holiday_dates')
                                                                ->label('Tanggal Libur / Dikecualikan')
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
                                            Section::make('Estimasi Kebutuhan Pegawai')
                                                ->description('Dihitung otomatis dari data Lokasi & Survei.')
                                                ->icon('heroicon-o-calculator')
                                                ->compact()
                                                ->schema([
                                                    Grid::make(3)->schema([
                                                        \Filament\Forms\Components\Placeholder::make('est_koordinator')
                                                            ->label('Koordinator')
                                                            ->content(function (callable $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                                                $count = self::getLocationEstimates($get, $record)['koordinator'];
                                                                return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-primary-600'>{$count}</span> <span class='text-xs text-gray-500'>orang (~1/lokasi)</span>");
                                                            }),
                                                        \Filament\Forms\Components\Placeholder::make('est_it')
                                                            ->label('Tim IT')
                                                            ->content(function (callable $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                                                $count = self::getLocationEstimates($get, $record)['it'];
                                                                return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-indigo-600'>{$count}</span> <span class='text-xs text-gray-500'>orang (~1/50 PC)</span>");
                                                            }),
                                                        \Filament\Forms\Components\Placeholder::make('est_pengawas')
                                                            ->label('Pengawas')
                                                            ->content(function (callable $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                                                $count = self::getLocationEstimates($get, $record)['pengawas'];
                                                                return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold text-emerald-600'>{$count}</span> <span class='text-xs text-gray-500'>orang (ruangan/25 PC)</span>");
                                                            }),
                                                    ]),
                                                ]),
                                            Repeater::make('eventEmployees')
                                                ->relationship()
                                                ->schema([
                                                    Select::make('role')
                                                        ->label('Peran')
                                                        ->multiple()
                                                        ->options([
                                                            'Koordinator' => 'Koordinator',
                                                            'IT' => 'IT',
                                                            'Pengawas' => 'Pengawas',
                                                        ])
                                                        ->required()
                                                        ->live()
                                                        ->columnSpan(1),
                                                    Select::make('employee_id')
                                                        ->label('Pegawai')
                                                        ->relationship('employee', 'name', modifyQueryUsing: function (\Illuminate\Database\Eloquent\Builder $query, callable $get) {
                                                            $roles = $get('role');
                                                            if (!empty($roles)) {
                                                                $query->where(function ($q) use ($roles) {
                                                                    foreach ($roles as $role) {
                                                                        $q->orWhereJsonContains('status', $role);
                                                                    }
                                                                });
                                                            }
                                                        })
                                                        ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record) => "{$record->name} - {$record->position} (NIP: {$record->employee_number})")
                                                        ->searchable(['name', 'employee_number', 'position'])
                                                        ->preload()
                                                        ->required()
                                                        ->live()
                                                        ->rules([
                                                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                                if (!$value) return;
                                                                
                                                                $currentInstitutions = $get('../../eventInstitutions') ?? [];
                                                                $currentDates = collect($currentInstitutions)
                                                                    ->filter(fn ($item) => !empty($item['start_date']) && !empty($item['end_date']))
                                                                    ->map(fn ($item) => ['start' => $item['start_date'], 'end' => $item['end_date']]);

                                                                if ($currentDates->isEmpty()) return;

                                                                $conflicts = \App\Models\EventEmployee::query()
                                                                    ->where('employee_id', $value)
                                                                    ->where('event_id', '!=', $get('../../id'))
                                                                    ->whereHas('event.eventInstitutions', function ($query) use ($currentDates) {
                                                                        $query->where(function ($q) use ($currentDates) {
                                                                            foreach ($currentDates as $date) {
                                                                                $q->orWhere(function ($inner) use ($date) {
                                                                                    $inner->where('start_date', '<=', $date['end'])
                                                                                          ->where('end_date', '>=', $date['start']);
                                                                                });
                                                                            }
                                                                        });
                                                                    })
                                                                    ->with('event')
                                                                    ->get();

                                                                if ($conflicts->isNotEmpty()) {
                                                                    $eventNames = $conflicts->map(fn ($c) => $c->event->name)->unique()->implode(', ');
                                                                    $fail("Pegawai ini sudah ditugaskan pada kegiatan: {$eventNames} di jadwal yang bersinggungan.");
                                                                }
                                                            },
                                                        ])
                                                        ->columnSpan(1),
                                                ])
                                                ->columns(2)
                                                ->collapsible()
                                                ->cloneable()
                                                ->itemLabel(fn (array $state): ?string => Employee::find($state['employee_id'] ?? null)?->name ?? 'Pegawai Baru')
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
                                                FileUpload::make('certificate_template')
                                                    ->label('Template Sertifikat (Latar Belakang)')
                                                    ->directory('events/certificates')
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
                            \Filament\Schemas\Components\Section::make('Informasi Utama')
                                ->description('Rangkuman identitas dan status kegiatan')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('rev_name')
                                        ->label('Nama Kegiatan')
                                        ->content(fn (callable $get) => $get('name') ?: '-'),
                                    \Filament\Forms\Components\Placeholder::make('rev_year')
                                        ->label('Tahun Formasi')
                                        ->content(fn (callable $get) => $get('formation_year') ?: '-'),
                                    \Filament\Forms\Components\Placeholder::make('rev_status')
                                        ->label('Status')
                                        ->content(fn (callable $get) => strtoupper($get('status') ?: '-')),
                                ])->columns(3),

                            \Filament\Schemas\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Schemas\Components\Section::make('Daftar Instansi')
                                        ->description('Instansi yang menjadi klien')
                                        ->icon('heroicon-o-building-office')
                                        ->schema([
                                            \Filament\Forms\Components\Placeholder::make('rev_inst')
                                                ->hiddenLabel()
                                                ->content(function (callable $get) {
                                                    $institutions = collect($get('eventInstitutions'))->filter(fn($i) => !empty($i['institution_id']));
                                                    if ($institutions->isEmpty()) return new \Illuminate\Support\HtmlString('<span class="text-gray-500 italic text-sm">Belum ada instansi.</span>');
                                                    
                                                    $html = $institutions->map(function ($item) {
                                                        $name = \App\Models\Institution::find($item['institution_id'])?->name ?? 'Unknown';
                                                        return "<div class='py-3 border-b border-gray-100 dark:border-white/5 last:border-0'>
                                                                    <div class='font-semibold text-sm text-gray-900 dark:text-white'>{$name}</div>
                                                                </div>";
                                                    })->implode('');
                                                    return new \Illuminate\Support\HtmlString($html);
                                                })
                                        ])->columnSpan(1),

                                    \Filament\Schemas\Components\Section::make('Tim Pelaksana')
                                        ->description('Personil penugasan SDM')
                                        ->icon('heroicon-o-users')
                                        ->schema([
                                            \Filament\Forms\Components\Placeholder::make('rev_emp')
                                                ->hiddenLabel()
                                                ->content(function (callable $get) {
                                                    $employees = collect($get('eventEmployees'))->filter(fn($e) => !empty($e['employee_id']));
                                                    if ($employees->isEmpty()) return new \Illuminate\Support\HtmlString('<span class="text-gray-500 italic text-sm">Belum ada pegawai.</span>');

                                                    $html = $employees->map(function ($item) {
                                                        $employee = \App\Models\Employee::find($item['employee_id']);
                                                        $roles = is_array($item['role']) ? implode(', ', $item['role']) : $item['role'];
                                                        return "<div class='py-3 border-b border-gray-100 dark:border-white/5 last:border-0'>
                                                                    <div class='font-semibold text-sm text-gray-900 dark:text-white'>{$employee?->name}</div>
                                                                    <div class='text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono'>NIP. {$employee?->employee_number} <span class='mx-1'>&bull;</span> <span class='font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400'>{$roles}</span></div>
                                                                </div>";
                                                    })->implode('');
                                                    return new \Illuminate\Support\HtmlString($html);
                                                })
                                        ])->columnSpan(1),
                                ]),

                            \Filament\Schemas\Components\Section::make('Distribusi Lokasi')
                                ->description('Jadwal pelaksanaan dan kuota per titik lokasi')
                                ->icon('heroicon-o-map-pin')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('rev_loc')
                                        ->hiddenLabel()
                                        ->content(function (callable $get) {
                                            $locations = collect($get('eventLocations'))->filter(fn($l) => !empty($l['location_id']));
                                            if ($locations->isEmpty()) return new \Illuminate\Support\HtmlString('<span class="text-gray-500 italic text-sm">Belum ada lokasi.</span>');

                                            $html = $locations->map(function ($item) {
                                                $name = \App\Models\Location::find($item['location_id'])?->name ?? 'Unknown';
                                                $start = $item['start_date'] ? \Carbon\Carbon::parse($item['start_date'])->translatedFormat('d M Y') : '-';
                                                $end = $item['end_date'] ? \Carbon\Carbon::parse($item['end_date'])->translatedFormat('d M Y') : '-';
                                                $participants = number_format((float) ($item['participants_count'] ?? 0));
                                                
                                                return "<div class='p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10'>
                                                            <div class='flex justify-between items-start mb-3'>
                                                                <div class='font-bold text-sm text-gray-900 dark:text-white leading-tight pr-4'>{$name}</div>
                                                                <div class='flex-shrink-0 text-[10px] font-black px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-lg tracking-widest uppercase'>{$participants} PAX</div>
                                                            </div>
                                                            <div class='flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium'>
                                                                {$start} &mdash; {$end}
                                                            </div>
                                                        </div>";
                                            })->implode('');
                                            
                                            return new \Illuminate\Support\HtmlString("<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4'>{$html}</div>");
                                        })
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
        $participants = (int) $get('participants_count');
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
            'participantsPerSession' => $participantsPerSession,
            'lastSessionParticipants' => $lastSessionParticipants,
            'totalParticipants' => $participants,
            'pcCapacity' => $capacity,
            'dailySchedule' => $dailySchedule,
        ];
    }

    private static function getLocationEstimates(callable $get, ?\Illuminate\Database\Eloquent\Model $record): array
    {
        $totalKoordinator = 0;
        $totalIT = 0;
        $totalPengawas = 0;

        // For edit mode: use saved record's eventLocations relationship
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
}
