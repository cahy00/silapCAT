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
    
    protected static ?string $modelLabel = 'Kegiatan';
    protected static ?string $pluralModelLabel = 'Kegiatan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Kegiatan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

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
                SchemaSection::make('Informasi Kegiatan')
                    ->description('Detail informasi dan klasifikasi operasional kegiatan seleksi.')
                    ->icon('heroicon-o-information-circle')
                    ->columns(12)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Kegiatan')
                            ->size(\Filament\Support\Enums\TextSize::Large)
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->icon('heroicon-o-flag')
                            ->columnSpan(8),

                        TextEntry::make('status')
                            ->label('Status Operasional')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'active' => 'success',
                                'completed' => 'info',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->columnSpan(4),

                        TextEntry::make('procurementType.name')
                            ->label('Jenis Pengadaan')
                            ->icon('heroicon-o-briefcase')
                            ->color('info')
                            ->columnSpan(3),

                        TextEntry::make('formation_year')
                            ->label('Tahun Formasi')
                            ->icon('heroicon-o-calendar-days')
                            ->columnSpan(3),

                        TextEntry::make('start_date')
                            ->label('Tanggal Mulai')
                            ->date()
                            ->icon('heroicon-o-play-circle')
                            ->columnSpan(3),

                        TextEntry::make('end_date')
                            ->label('Tanggal Selesai')
                            ->date()
                            ->icon('heroicon-o-stop-circle')
                            ->columnSpan(3),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tidak ada deskripsi tambahan.')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                SchemaTabs::make('Manajemen Detil')
                    ->tabs([
                        SchemaTab::make('Distribusi Lokasi & Instansi')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextEntry::make('dashboard_lokasi')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(function (Event $record) {
                                        $locations = $record->eventLocations;
                                        if ($locations->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 24px; color: #9ca3af; font-style: italic; font-size: 13px;'>Belum ada lokasi yang ditambahkan.</div>");
                                        }

                                        $grandTotal = 0;
                                        $cards = '';

                                        foreach ($locations as $loc) {
                                            $name = e($loc->location?->name ?? 'Unknown');
                                            $start = $loc->start_date ? \Carbon\Carbon::parse($loc->start_date)->translatedFormat('d M Y') : '-';
                                            $end = $loc->end_date ? \Carbon\Carbon::parse($loc->end_date)->translatedFormat('d M Y') : '-';
                                            $institutions = $loc->eventLocationInstitutions;
                                            $locTotal = $institutions->sum('participants_count');
                                            $grandTotal += $locTotal;

                                            $instRows = '';
                                            if ($institutions->isEmpty()) {
                                                $instRows = "<tr><td colspan='2' style='padding: 12px 14px; font-size: 13px; color: #9ca3af; font-style: italic; text-align: center;'>Belum ada instansi</td></tr>";
                                            } else {
                                                $no = 1;
                                                foreach ($institutions as $inst) {
                                                    $instName = e($inst->institution?->name ?? '-');
                                                    $count = (int) $inst->participants_count;
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
                                                        <span style='display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #ecfdf5; color: #047857; letter-spacing: 0.05em; flex-shrink: 0;'>" . number_format($locTotal) . " Peserta</span>
                                                    </div>
                                                    <div style='max-height: 250px; overflow-y: auto;'>
                                                        <table style='width: 100%; border-collapse: collapse;'>
                                                            <tbody>{$instRows}</tbody>
                                                        </table>
                                                    </div>
                                                </div>";
                                        }

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

                        SchemaTab::make('Tim Pelaksana')
                            ->icon('heroicon-o-users')
                            ->schema([
                                TextEntry::make('dashboard_tim')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(function (Event $record) {
                                        $employees = $record->eventEmployees()->with('employee')->get();
                                        
                                        $roleFieldMap = [
                                            'Koordinator' => ['icon' => '👤', 'color' => '#6366f1', 'bg' => '#eef2ff', 'badgeText' => '#4338ca'],
                                            'IT' => ['icon' => '💻', 'color' => '#0ea5e9', 'bg' => '#f0f9ff', 'badgeText' => '#0369a1'],
                                            'Pengawas' => ['icon' => '🛡️', 'color' => '#10b981', 'bg' => '#ecfdf5', 'badgeText' => '#047857'],
                                        ];

                                        if ($employees->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 24px; color: #9ca3af; font-style: italic; font-size: 13px;'>Belum ada pegawai yang ditugaskan.</div>");
                                        }

                                        $columns = '';
                                        foreach ($roleFieldMap as $roleName => $config) {
                                            $roleEmps = $employees->filter(function ($ee) use ($roleName) {
                                                $roles = is_array($ee->role) ? $ee->role : (is_string($ee->role) ? json_decode($ee->role, true) ?? [$ee->role] : []);
                                                return in_array($roleName, $roles);
                                            });
                                            $count = $roleEmps->count();

                                            $items = '';
                                            if ($roleEmps->isEmpty()) {
                                                $items = "<div style='padding: 12px 16px; font-size: 13px; color: #9ca3af; font-style: italic;'>Belum ada</div>";
                                            } else {
                                                foreach ($roleEmps as $empRel) {
                                                    $emp = $empRel->employee;
                                                    if (!$emp) continue;
                                                    $initial = strtoupper(mb_substr($emp->name, 0, 1));
                                                    $items .= "
                                                        <div style='padding: 10px 16px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;'>
                                                            <div style='width: 32px; height: 32px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: {$config['color']}; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid {$config['color']}20;'>{$initial}</div>
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
                                                        <span style='display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; background: #ffffff; color: {$config['badgeText']}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);'>{$count} orang</span>
                                                    </div>
                                                    <div style='max-height: 300px; overflow-y: auto; background: #fafafa;'>{$items}</div>
                                                </div>";
                                        }

                                        return new \Illuminate\Support\HtmlString("<div style='display: flex; gap: 16px; flex-wrap: wrap;'>{$columns}</div>");
                                    }),
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

                        SchemaTab::make('Rekapitulasi Laporan & Nilai')
                            ->icon('heroicon-o-chart-bar-square')
                            ->schema([
                                TextEntry::make('dashboard_rekap_laporan')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(function (Event $record) {
                                        $reports = $record->reports()->with(['eventLocation.location', 'user'])->orderBy('report_date', 'desc')->orderBy('session_name', 'asc')->get();

                                        if ($reports->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 32px; color: #9ca3af; font-style: italic; font-size: 13px; background: #f9fafb; border: 1px dashed #e5e7eb; border-radius: 12px;'>Belum ada laporan harian yang masuk untuk kegiatan ini.</div>");
                                        }

                                        $totalReports = $reports->count();
                                        $totalParticipants = $reports->sum('total_participants');
                                        $presentCount = $reports->sum('present_count');
                                        $absentCount = $reports->sum('absent_count');
                                        $highestScore = $reports->max('highest_score') ?? 0;
                                        $minReports = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
                                        $lowestScore = $minReports->isNotEmpty() ? $minReports->min('lowest_score') : ($reports->min('lowest_score') ?? 0);

                                        $statsGrid = "
                                            <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px;'>
                                                <div style='background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #4338ca; text-transform: uppercase;'>Total Laporan</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #312e81; margin-top: 4px;'>" . number_format($totalReports) . " <span style='font-size: 12px; font-weight: 600;'>Sesi</span></div>
                                                </div>
                                                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase;'>Total Hadir</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #14532d; margin-top: 4px;'>" . number_format($presentCount) . " <span style='font-size: 12px; font-weight: 600;'>Peserta</span></div>
                                                </div>
                                                <div style='background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #b91c1c; text-transform: uppercase;'>Total Absen</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #7f1d1d; margin-top: 4px;'>" . number_format($absentCount) . " <span style='font-size: 12px; font-weight: 600;'>Peserta</span></div>
                                                </div>
                                                <div style='background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase;'>Nilai Tertinggi</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #0c4a6e; margin-top: 4px;'>" . number_format($highestScore) . "</div>
                                                </div>
                                                <div style='background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #b45309; text-transform: uppercase;'>Nilai Terendah</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #78350f; margin-top: 4px;'>" . number_format($lowestScore) . "</div>
                                                </div>
                                            </div>";

                                        $rows = '';
                                        $no = 1;
                                        foreach ($reports as $rep) {
                                            $date = $rep->report_date ? \Carbon\Carbon::parse($rep->report_date)->translatedFormat('d M Y') : '-';
                                            $session = e($rep->session_name ?? '-');
                                            $locName = e($rep->eventLocation?->location?->name ?? '-');
                                            $present = number_format((int) $rep->present_count);
                                            $absent = number_format((int) $rep->absent_count);
                                            $total = number_format((int) $rep->total_participants);
                                            $high = $rep->highest_score !== null ? number_format($rep->highest_score) : '-';
                                            $low = $rep->lowest_score !== null ? number_format($rep->lowest_score) : '-';
                                            $operator = e($rep->user?->name ?? '-');
                                            $bgColor = $no % 2 === 0 ? 'background-color: #f9fafb;' : 'background-color: #ffffff;';

                                            $rows .= "
                                                <tr style='border-bottom: 1px solid #e5e7eb; {$bgColor}'>
                                                    <td style='padding: 12px 14px; font-size: 13px; color: #6b7280; text-align: center;'>{$no}</td>
                                                    <td style='padding: 12px 14px; font-size: 13px; color: #111827; font-weight: 600;'>
                                                        {$date}
                                                        <span style='display: inline-block; margin-left: 6px; padding: 2px 6px; background: #eef2ff; color: #4338ca; border-radius: 4px; font-size: 11px; font-weight: 700;'>{$session}</span>
                                                    </td>
                                                    <td style='padding: 12px 14px; font-size: 13px; color: #374151;'>{$locName}</td>
                                                    <td style='padding: 12px 14px; font-size: 13px; text-align: center;'>
                                                        <span style='color: #15803d; font-weight: 700;'>{$present} Hadir</span> /
                                                        <span style='color: #b91c1c; font-weight: 600;'>{$absent} Absen</span>
                                                        <div style='font-size: 11px; color: #6b7280;'>Total: {$total}</div>
                                                    </td>
                                                    <td style='padding: 12px 14px; font-size: 14px; font-weight: 800; color: #0369a1; text-align: center;'>{$high}</td>
                                                    <td style='padding: 12px 14px; font-size: 14px; font-weight: 800; color: #b45309; text-align: center;'>{$low}</td>
                                                    <td style='padding: 12px 14px; font-size: 12px; color: #4b5563;'>{$operator}</td>
                                                </tr>";
                                            $no++;
                                        }

                                        $table = "
                                            <div style='border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                                                <div style='overflow-x: auto;'>
                                                    <table style='width: 100%; border-collapse: collapse; text-align: left;'>
                                                        <thead>
                                                            <tr style='background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;'>
                                                                <th style='padding: 12px 14px; width: 40px; text-align: center;'>No</th>
                                                                <th style='padding: 12px 14px;'>Tanggal & Sesi</th>
                                                                <th style='padding: 12px 14px;'>Titik Lokasi</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Rekap Kehadiran</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Nilai Tertinggi</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Nilai Terendah</th>
                                                                <th style='padding: 12px 14px;'>Pelapor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>{$rows}</tbody>
                                                    </table>
                                                </div>
                                            </div>";

                                        return new \Illuminate\Support\HtmlString("<div>{$statsGrid}{$table}</div>");
                                    }),
                            ]),

                        SchemaTab::make('Daftar Peserta & Nilai Ujian')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextEntry::make('dashboard_daftar_peserta')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(function (Event $record) {
                                        $scores = $record->examScores()->orderBy('total_score', 'desc')->get();

                                        if ($scores->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString("<div style='text-align: center; padding: 32px; color: #9ca3af; font-style: italic; font-size: 13px; background: #f9fafb; border: 1px dashed #e5e7eb; border-radius: 12px;'>Belum ada data peserta dan nilai ujian yang masuk untuk kegiatan ini.</div>");
                                        }

                                        $totalParticipants = $scores->count();
                                        $lulusCount = $scores->where('status', 'Lulus')->count();
                                        $tidakLulusCount = $scores->where('status', 'Tidak Lulus')->count();
                                        $avgCat = $scores->avg('cat_score') ?? 0;
                                        $avgTotal = $scores->avg('total_score') ?? 0;

                                        $statsGrid = "
                                            <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px;'>
                                                <div style='background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #4338ca; text-transform: uppercase;'>Total Peserta</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #312e81; margin-top: 4px;'>" . number_format($totalParticipants) . " <span style='font-size: 12px; font-weight: 600;'>Orang</span></div>
                                                </div>
                                                <div style='background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase;'>Lulus Ujian</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #14532d; margin-top: 4px;'>" . number_format($lulusCount) . " <span style='font-size: 12px; font-weight: 600;'>Orang</span></div>
                                                </div>
                                                <div style='background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #b91c1c; text-transform: uppercase;'>Tidak Lulus</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #7f1d1d; margin-top: 4px;'>" . number_format($tidakLulusCount) . " <span style='font-size: 12px; font-weight: 600;'>Orang</span></div>
                                                </div>
                                                <div style='background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase;'>Rata-rata CAT</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #0c4a6e; margin-top: 4px;'>" . number_format($avgCat, 2) . "</div>
                                                </div>
                                                <div style='background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px;'>
                                                    <div style='font-size: 11px; font-weight: 700; color: #b45309; text-transform: uppercase;'>Rata-rata Nilai Akhir</div>
                                                    <div style='font-size: 20px; font-weight: 900; color: #78350f; margin-top: 4px;'>" . number_format($avgTotal, 2) . "</div>
                                                </div>
                                            </div>";

                                        $rows = '';
                                        $no = 1;
                                        foreach ($scores as $sc) {
                                            $name = e($sc->name ?? '-');
                                            $nip = e($sc->employee_number ?? '-');
                                            $position = e($sc->position ?? '-');
                                            $inst = e($sc->institution ?? '-');
                                            $type = e($sc->exam_type ?? '-');
                                            $cat = $sc->cat_score !== null ? number_format($sc->cat_score, 2) : '-';
                                            $iv = $sc->interview_score !== null ? number_format($sc->interview_score, 2) : '-';
                                            $total = $sc->total_score !== null ? number_format($sc->total_score, 2) : '-';
                                            $status = $sc->status ?? '-';

                                            $statusBadge = match ($status) {
                                                'Lulus' => "<span style='display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;'>LULUS</span>",
                                                'Tidak Lulus' => "<span style='display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;'>TIDAK LULUS</span>",
                                                default => "<span style='display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #f3f4f6; color: #4b5563;'>{$status}</span>",
                                            };

                                            $typeBadge = "<span style='display: inline-block; padding: 2px 6px; background: #eef2ff; color: #4338ca; border-radius: 4px; font-size: 11px; font-weight: 700;'>{$type}</span>";

                                            $bgColor = $no % 2 === 0 ? 'background-color: #f9fafb;' : 'background-color: #ffffff;';

                                            $rows .= "
                                                <tr style='border-bottom: 1px solid #e5e7eb; {$bgColor}'>
                                                    <td style='padding: 12px 14px; font-size: 13px; font-weight: 700; color: #6b7280; text-align: center;'>#{$no}</td>
                                                    <td style='padding: 12px 14px;'>
                                                        <div style='font-size: 13px; font-weight: 700; color: #111827;'>{$name}</div>
                                                        <div style='font-size: 11px; color: #6b7280; font-family: monospace;'>NIP. {$nip}</div>
                                                        <div style='font-size: 11px; color: #4b5563; font-style: italic;'>{$position}</div>
                                                    </td>
                                                    <td style='padding: 12px 14px; font-size: 13px; color: #374151;'>{$inst}</td>
                                                    <td style='padding: 12px 14px; text-align: center;'>{$typeBadge}</td>
                                                    <td style='padding: 12px 14px; font-size: 13px; font-weight: 600; color: #0369a1; text-align: center;'>{$cat}</td>
                                                    <td style='padding: 12px 14px; font-size: 13px; font-weight: 600; color: #4b5563; text-align: center;'>{$iv}</td>
                                                    <td style='padding: 12px 14px; font-size: 14px; font-weight: 900; color: #4338ca; text-align: center;'>{$total}</td>
                                                    <td style='padding: 12px 14px; text-align: center;'>{$statusBadge}</td>
                                                </tr>";
                                            $no++;
                                        }

                                        $table = "
                                            <div style='border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                                                <div style='overflow-x: auto;'>
                                                    <table style='width: 100%; border-collapse: collapse; text-align: left;'>
                                                        <thead>
                                                            <tr style='background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;'>
                                                                <th style='padding: 12px 14px; width: 40px; text-align: center;'>Peringkat</th>
                                                                <th style='padding: 12px 14px;'>Identitas Peserta</th>
                                                                <th style='padding: 12px 14px;'>Instansi</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Jenis Ujian</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Nilai CAT</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Wawancara</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Nilai Akhir</th>
                                                                <th style='padding: 12px 14px; text-align: center;'>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>{$rows}</tbody>
                                                    </table>
                                                </div>
                                            </div>";

                                        return new \Illuminate\Support\HtmlString("<div>{$statsGrid}{$table}</div>");
                                    }),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['eventInstitutions.institution', 'eventLocations.location', 'reports.eventLocation.location', 'reports.user', 'examScores', 'procurementType.procurementCategory']);
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
