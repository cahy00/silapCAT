<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Event;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('start_date', 'desc')
            ->emptyStateHeading('Belum Ada Kegiatan')
            ->emptyStateDescription('Daftar kegiatan yang Anda buat akan muncul di sini.')
            ->emptyStateIcon(null)
            ->columns([
                TextColumn::make('name')
                    ->label('INFORMASI KEGIATAN & TITIK LOKASI')
                    ->searchable(['name'])
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $name = e($record->name);
                        $procurementTypeName = e($record->procurementType?->name ?? '');

                        // Status badge
                        $status = $record->status;
                        $statusConfig = match ($status) {
                            'aktif' => ['bg' => '#22c55e', 'text' => '#ffffff', 'label' => '● AKTIF', 'glow' => 'box-shadow: 0 0 10px rgba(34,197,94,0.4);'],
                            'selesai' => ['bg' => '#64748b', 'text' => '#ffffff', 'label' => '✓ SELESAI', 'glow' => ''],
                            'cancelled' => ['bg' => '#f43f5e', 'text' => '#ffffff', 'label' => '✕ DIBATALKAN', 'glow' => ''],
                            default => ['bg' => '#f59e0b', 'text' => '#ffffff', 'label' => '◌ DRAFT', 'glow' => ''],
                        };

                        $statusBadge = "<span style='display:inline-flex;align-items:center;padding:3px 9px;border-radius:9999px;font-size:10px;font-weight:900;background:{$statusConfig['bg']};color:{$statusConfig['text']};letter-spacing:0.05em;{$statusConfig['glow']}'>{$statusConfig['label']}</span>";

                        $categoryBadge = $procurementTypeName ? "
                            <span class='sc-cat-badge'>
                                {$procurementTypeName}
                            </span>" : "";

                        // Location cards
                        $locationCards = '';
                        $locations = $record->eventLocations;
                        $grandTotalParticipants = 0;

                        if ($locations->isEmpty()) {
                            $locationCards = "<div class='sc-subtext' style='margin-top:6px;font-style:italic;'>📍 Belum ada titik lokasi</div>";
                        } else {
                            foreach ($locations as $el) {
                                $locName = e($el->location?->name ?? '-');
                                $locCity = e($el->location?->city ?? '');

                                $instPills = '';
                                $locParticipants = 0;
                                foreach ($el->eventLocationInstitutions as $eli) {
                                    $iName = e($eli->institution?->name ?? '-');
                                    $pCount = number_format((int) $eli->participants_count);
                                    $locParticipants += (int) $eli->participants_count;
                                    $instPills .= "
                                        <div class='sc-subtext' style='display:flex;align-items:center;gap:4px;font-size:11px;'>
                                            <span>🏢</span>
                                            <span>{$iName}</span>
                                            <span style='font-size:11px;font-weight:800;color:#6366f1;'>({$pCount})</span>
                                        </div>";
                                }

                                $grandTotalParticipants += $locParticipants;

                                $locationCards .= "
                                    <div class='sc-border-accent' style='margin-top:6px;padding-left:10px;'>
                                        <div style='display:flex;align-items:center;gap:5px;'>
                                            <span style='font-size:12px;'>📍</span>
                                            <span class='sc-loc-name'>{$locName}</span>
                                            " . ($locCity ? "<span class='sc-subtext'>· {$locCity}</span>" : "") . "
                                        </div>
                                        <div style='margin-top:2px;margin-left:18px;display:flex;flex-direction:column;gap:2px;'>
                                            {$instPills}
                                        </div>
                                    </div>";
                            }
                        }

                        // Total Peserta
                        $totalPesertaBadge = '';
                        if ($grandTotalParticipants > 0) {
                            $totalFormatted = number_format($grandTotalParticipants);
                            $totalPesertaBadge = "
                                <div class='sc-peserta-badge'>
                                    <span style='font-size:14px;'>👥</span>
                                    <span class='sc-peserta-num'>{$totalFormatted}</span>
                                    <span class='sc-peserta-lbl'>Peserta</span>
                                </div>";
                        }

                        return new HtmlString("
                            <style>
                                .sc-event-title { font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: -0.01em; line-height: 1.35; color: #0f172a; }
                                .dark .sc-event-title { color: #f8fafc; }
                                .sc-loc-name { font-weight: 700; font-size: 12px; color: #1e293b; }
                                .dark .sc-loc-name { color: #f1f5f9; }
                                .sc-subtext { font-size: 11px; color: #64748b; }
                                .dark .sc-subtext { color: #94a3b8; }
                                .sc-border-accent { border-left: 2px solid #818cf8; }
                                .dark .sc-border-accent { border-left: 2px solid #6366f1; }
                                .sc-peserta-badge { margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: #eef2ff; border: 1px solid #e0e7ff; width: fit-content; }
                                .dark .sc-peserta-badge { background: rgba(99, 102, 241, 0.18); border-color: rgba(99, 102, 241, 0.35); }
                                .sc-peserta-num { font-size: 14px; font-weight: 900; color: #4f46e5; line-height: 1; }
                                .dark .sc-peserta-num { color: #818cf8; }
                                .sc-peserta-lbl { font-size: 10px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 0.08em; }
                                .dark .sc-peserta-lbl { color: #c7d2fe; }
                                .sc-cat-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 700; background: #eef2ff; color: #4338ca; border: 1px solid rgba(99,102,241,0.25); text-transform: uppercase; letter-spacing: 0.05em; }
                                .dark .sc-cat-badge { background: rgba(99, 102, 241, 0.2); color: #c7d2fe; border-color: rgba(99, 102, 241, 0.4); }
                                .sc-cal-card { display: flex; flex-direction: column; align-items: center; width: 42px; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; flex-shrink: 0; background: #ffffff; }
                                .dark .sc-cal-card { border-color: #334155; background: #1e293b; }
                                .sc-cal-day { width: 100%; text-align: center; font-size: 16px; font-weight: 900; color: #1e293b; padding: 2px 0; }
                                .dark .sc-cal-day { color: #f8fafc; }
                                .sc-schedule-text { font-size: 12px; font-weight: 700; color: #1e293b; line-height: 1.3; }
                                .dark .sc-schedule-text { color: #f1f5f9; }
                                .sc-pill-tag { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 4px; background: #f1f5f9; font-size: 10px; font-weight: 700; color: #475569; }
                                .dark .sc-pill-tag { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
                                .sc-stat-val { font-size: 11px; font-weight: 900; color: #1e293b; }
                                .dark .sc-stat-val { color: #f1f5f9; }
                                .sc-sesi-title { font-size: 11px; font-weight: 700; color: #475569; }
                                .dark .sc-sesi-title { color: #cbd5e1; }
                                .sc-track-circle { stroke: #e2e8f0; }
                                .dark .sc-track-circle { stroke: #334155; }
                                .sc-doc-label { font-size: 10px; font-weight: 500; color: #334155; }
                                .dark .sc-doc-label { color: #cbd5e1; }
                                .sc-doc-label-empty { font-size: 10px; font-weight: 500; color: #94a3b8; }
                                .dark .sc-doc-label-empty { color: #64748b; }
                                .sc-doc-badge-complete { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 900; background: #dcfce7; color: #15803d; }
                                .dark .sc-doc-badge-complete { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.35); }
                                .sc-doc-badge-partial { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 900; background: #fef3c7; color: #b45309; }
                                .dark .sc-doc-badge-partial { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); }
                                .sc-doc-badge-empty { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 900; background: #f1f5f9; color: #64748b; }
                                .dark .sc-doc-badge-empty { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.25); }
                                .sc-seg-empty { background: #e2e8f0; }
                                .dark .sc-seg-empty { background: #334155; }
                            </style>
                            <div style='display:flex;flex-direction:column;padding:10px 0;min-width:320px;max-width:550px;'>
                                <div style='display:flex;align-items:center;gap:6px;flex-wrap:wrap;'>
                                    {$statusBadge}
                                    {$categoryBadge}
                                </div>
                                <h3 class='sc-event-title' style='margin-top:6px;'>{$name}</h3>
                                <div>
                                    {$locationCards}
                                </div>
                                {$totalPesertaBadge}
                            </div>
                        ");
                    }),

                TextColumn::make('start_date')
                    ->label('JADWAL EVENT')
                    ->sortable()
                    ->html()
                    ->getStateUsing(fn (Event $record) => $record->id)
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $startDate = $record->start_date ? \Carbon\Carbon::parse($record->start_date) : null;
                        $endDate = $record->end_date ? \Carbon\Carbon::parse($record->end_date) : null;

                        if (!$startDate || !$endDate) {
                            return new HtmlString("
                                <div style='display:flex;align-items:center;gap:8px;padding:10px 0;'>
                                    <span style='font-size:18px;opacity:0.4;'>📅</span>
                                    <span class='sc-subtext' style='font-style:italic;'>Belum dijadwalkan</span>
                                </div>
                            ");
                        }

                        $startDay = $startDate->translatedFormat('d');
                        $startMonth = $startDate->translatedFormat('M');
                        $end = $endDate->translatedFormat('d M Y');
                        $locCount = $record->eventLocations->count();
                        $duration = $startDate->diffInDays($endDate) + 1;

                        // Determine if event is ongoing, upcoming, or past
                        $now = now()->startOfDay();
                        $isOngoing = $now->between($startDate, $endDate);
                        $isPast = $now->gt($endDate);

                        $timelineBorder = $isOngoing ? '#22c55e' : ($isPast ? '#94a3b8' : '#f59e0b');
                        $calendarBg = $isOngoing ? '#22c55e' : ($isPast ? '#64748b' : '#f59e0b');

                        return new HtmlString("
                            <div style='display:flex;align-items:flex-start;gap:8px;padding:10px 0;min-width:150px;'>
                                <div class='sc-cal-card'>
                                    <div style='background:{$calendarBg};width:100%;text-align:center;font-size:9px;font-weight:900;color:#ffffff;text-transform:uppercase;letter-spacing:0.05em;padding:2px 0;'>{$startMonth}</div>
                                    <div class='sc-cal-day'>{$startDay}</div>
                                </div>
                                <div style='display:flex;flex-direction:column;gap:4px;border-left:2px solid {$timelineBorder};padding-left:8px;'>
                                    <div class='sc-schedule-text'>{$startDate->translatedFormat('d M')} — {$end}</div>
                                    <div style='display:flex;align-items:center;gap:4px;flex-wrap:wrap;'>
                                        <span class='sc-pill-tag'>
                                            ⏱️ {$duration} Hari
                                        </span>
                                        <span class='sc-pill-tag'>
                                            📍 {$locCount} Tilok
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('laporan_kehadiran')
                    ->label('LAPORAN & KEHADIRAN')
                    ->html()
                    ->getStateUsing(fn(Event $record) => $record->reports->count())
                    ->formatStateUsing(function (Event $record, $state): HtmlString {
                        $sesiCount = (int) $state;
                        if ($sesiCount === 0) {
                            return new HtmlString("
                                <div style='display:flex;flex-direction:column;align-items:center;padding:10px 0;gap:4px;min-width:140px;'>
                                    <span style='font-size:24px;opacity:0.3;'>📊</span>
                                    <span class='sc-subtext' style='font-weight:600;'>Belum ada laporan</span>
                                </div>
                            ");
                        }

                        $pesertaSesi = $record->reports->sum('total_participants');
                        $hadir = $record->reports->sum('present_count');
                        $absen = $record->reports->sum('absent_count');
                        $persen = $pesertaSesi > 0 ? round(($hadir / $pesertaSesi) * 100, 1) : 0;

                        // Ring via inline SVG with explicit width/height
                        $radius = 18;
                        $circumference = 2 * M_PI * $radius;
                        $offset = $circumference - ($persen / 100) * $circumference;
                        $ringColor = $persen >= 90 ? '#22c55e' : ($persen >= 75 ? '#f59e0b' : '#ef4444');

                        $hadirFormatted = number_format($hadir);
                        $absenFormatted = number_format($absen);

                        return new HtmlString("
                            <div style='display:flex;align-items:center;gap:10px;padding:10px 0;min-width:180px;'>
                                <div style='position:relative;width:48px;height:48px;flex-shrink:0;'>
                                    <svg viewBox='0 0 44 44' style='width:48px;height:48px;transform:rotate(-90deg);display:block;'>
                                        <circle class='sc-track-circle' cx='22' cy='22' r='{$radius}' fill='none' stroke-width='4'/>
                                        <circle cx='22' cy='22' r='{$radius}' fill='none' stroke-width='4' stroke='{$ringColor}' stroke-linecap='round' stroke-dasharray='{$circumference}' stroke-dashoffset='{$offset}'/>
                                    </svg>
                                    <div style='position:absolute;inset:0;display:flex;align-items:center;justify-content:center;'>
                                        <span class='sc-stat-val'>{$persen}%</span>
                                    </div>
                                </div>
                                <div style='display:flex;flex-direction:column;gap:3px;'>
                                    <span class='sc-sesi-title'>{$sesiCount} Sesi Laporan</span>
                                    <div style='display:flex;align-items:center;gap:4px;'>
                                        <span style='font-size:10px;font-weight:700;color:#22c55e;'>✔ {$hadirFormatted}</span>
                                        <span class='sc-subtext'>·</span>
                                        <span style='font-size:10px;font-weight:700;color:#ef4444;'>✖ {$absenFormatted}</span>
                                    </div>
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('status_dokumen')
                    ->label('DOKUMEN')
                    ->html()
                    ->getStateUsing(function (Event $record): int {
                        $docs = [
                            $record->doc_implementation_report,
                            $record->doc_team_decree,
                            $record->doc_ba_catos,
                            $record->doc_institution_announcement,
                        ];

                        return collect($docs)->filter(fn($doc) => !empty($doc))->count();
                    })
                    ->formatStateUsing(function (Event $record, $state): HtmlString {
                        $total = 4;
                        $uploadedCount = (int) $state;

                        $docNames = ['Laporan', 'SK Tim', 'BA CATOS', 'Pengumuman'];
                        $docFields = [
                            $record->doc_implementation_report,
                            $record->doc_team_decree,
                            $record->doc_ba_catos,
                            $record->doc_institution_announcement,
                        ];

                        // Segmented progress bar
                        $segments = '';
                        for ($i = 0; $i < $total; $i++) {
                            $filled = !empty($docFields[$i]);
                            $bgClass = $filled ? "style='background:#22c55e;'" : "class='sc-seg-empty'";
                            $rounded = '';
                            if ($i === 0) $rounded = 'border-radius: 4px 0 0 4px;';
                            if ($i === $total - 1) $rounded = 'border-radius: 0 4px 4px 0;';
                            $segments .= "<div {$bgClass} style='flex:1;height:6px;{$rounded}'></div>";
                        }

                        // Status label
                        if ($uploadedCount === $total) {
                            $labelBadge = "<span class='sc-doc-badge-complete'>✅ LENGKAP</span>";
                        } elseif ($uploadedCount === 0) {
                            $labelBadge = "<span class='sc-doc-badge-empty'>⭕ BELUM ADA</span>";
                        } else {
                            $labelBadge = "<span class='sc-doc-badge-partial'>⚠️ {$uploadedCount}/{$total}</span>";
                        }

                        // Individual doc indicators
                        $docIndicators = '';
                        for ($i = 0; $i < $total; $i++) {
                            $filled = !empty($docFields[$i]);
                            $icon = $filled ? '✅' : '▫️';
                            $labelClass = $filled ? 'sc-doc-label' : 'sc-doc-label-empty';
                            $docIndicators .= "<div style='display:flex;align-items:center;gap:4px;'><span style='font-size:10px;'>{$icon}</span><span class='{$labelClass}'>{$docNames[$i]}</span></div>";
                        }

                        return new HtmlString("
                            <div style='display:flex;flex-direction:column;padding:10px 0;gap:6px;min-width:130px;'>
                                {$labelBadge}
                                <div style='display:flex;gap:3px;width:100%;'>{$segments}</div>
                                <div style='display:flex;flex-direction:column;gap:2px;'>
                                    {$docIndicators}
                                </div>
                            </div>
                        ");
                    })
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('procurement_category')
                    ->label('Kategori Pengadaan')
                    ->options(\App\Models\ProcurementCategory::pluck('name', 'id'))
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('procurementType', function ($q) use ($data) {
                                $q->where('procurement_category_id', $data['value']);
                            });
                        }
                    }),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'DRAFT',
                        'active' => 'ACTIVE',
                        'completed' => 'COMPLETED',
                        'cancelled' => 'CANCELLED',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('formation_year')
                    ->label('Tahun Pembentukan')
                    ->options(fn() => Event::distinct()->pluck('formation_year', 'formation_year')->filter()->toArray()),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->label('Lihat')
                        ->icon(null)
                        ->color('info'),
                    \Filament\Actions\Action::make('download_pdf')
                        ->label('Cetak PDF')
                        ->icon(null)
                        ->color('success')
                        ->url(fn (Event $record) => route('events.pdf', $record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\Action::make('upload_dokumen')
                        ->label('Upload Dokumen')
                        ->icon(null)
                        ->color('warning')
                        ->form([
                            \Filament\Schemas\Components\Grid::make(2)->schema([
                                \Filament\Forms\Components\FileUpload::make('doc_implementation_report')
                                    ->label('Laporan Pelaksanaan')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_team_decree')
                                    ->label('SK Tim Pelaksana')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_ba_catos')
                                    ->label('BA CATOS')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_institution_announcement')
                                    ->label('Pengumuman Instansi')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                            ]),
                        ])
                        ->mountUsing(fn ($form, Event $record) => $form->fill([
                            'doc_implementation_report' => $record->doc_implementation_report,
                            'doc_team_decree' => $record->doc_team_decree,
                            'doc_ba_catos' => $record->doc_ba_catos,
                            'doc_institution_announcement' => $record->doc_institution_announcement,
                        ]))
                        ->action(function (Event $record, array $data): void {
                            $record->update([
                                'doc_implementation_report' => $data['doc_implementation_report'] ?? $record->doc_implementation_report,
                                'doc_team_decree' => $data['doc_team_decree'] ?? $record->doc_team_decree,
                                'doc_ba_catos' => $data['doc_ba_catos'] ?? $record->doc_ba_catos,
                                'doc_institution_announcement' => $data['doc_institution_announcement'] ?? $record->doc_institution_announcement,
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Dokumen berhasil diupload')
                                ->success()
                                ->send();
                        })
                        ->visible(function (Event $record): bool {
                            if (! auth()->user()?->can('Update:Event')) {
                                return false;
                            }
                            $docs = [
                                $record->doc_implementation_report,
                                $record->doc_team_decree,
                                $record->doc_ba_catos,
                                $record->doc_institution_announcement,
                            ];
                            return collect($docs)->filter(fn($doc) => !empty($doc))->count() < 4;
                        }),
                    \Filament\Actions\Action::make('download_zip')
                        ->label('Unduh ZIP Dokumen')
                        ->icon(null)
                        ->color('warning')
                        ->url(fn (Event $record) => route('events.documents-zip', $record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon(null)
                        ->color('gray'),
                    \Filament\Actions\ReplicateAction::make()
                        ->label('Duplikasi Kegiatan')
                        ->icon(null)
                        ->color('info')
                        ->modalHeading('Duplikasi Kegiatan Ini')
                        ->modalDescription('Salin informasi global kegiatan ini menjadi draft baru? Anda tinggal mengubah atau menambahkan titik lokasi dan instansinya saja tanpa perlu ketik dari awal.')
                        ->beforeReplicaSaved(function (Event $replica) {
                            $replica->status = 'draft';
                            $replica->doc_implementation_report = null;
                            $replica->doc_team_decree = null;
                            $replica->doc_ba_catos = null;
                            $replica->doc_institution_announcement = null;
                        })
                        ->after(function (Event $replica) {
                            \Filament\Notifications\Notification::make()
                                ->title('Kegiatan berhasil diduplikasi')
                                ->body('Salinan kegiatan baru telah dibuat dengan status DRAFT.')
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah')
                        ->icon(null)
                        ->color('primary'),
                    \Filament\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon(null),
                ])
                ->label('Aksi')
                ->icon(null)
                ->button()
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('export_zip_bulk')
                        ->label('Ekspor ZIP Dokumen Terpilih')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->to(route('events.bulk-documents-zip', ['ids' => $ids]));
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
