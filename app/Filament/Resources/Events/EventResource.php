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

    protected static ?int $navigationSort = 1;

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
                                TextEntry::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->date()
                                    ->icon('heroicon-o-play-circle'),
                                TextEntry::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->date()
                                    ->icon('heroicon-o-stop-circle'),
                            ])
                            ->columnSpan(4),
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
                                                        <span style='display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #ecfdf5; color: #047857; letter-spacing: 0.05em; flex-shrink: 0;'>" . number_format($locTotal) . " PAX</span>
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
