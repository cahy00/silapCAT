<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

/**
 * Definisi kolom tabel daftar Kegiatan (desain kartu ringkas).
 */
class EventColumns
{
    public static function make(): array
    {
        return [
            self::eventColumn(),
            self::scheduleColumn(),
            self::attendanceColumn(),
            self::documentColumn(),
        ];
    }

    /**
     * CSS bersama untuk seluruh kolom (disisipkan sekali per baris pada kolom pertama).
     */
    protected static function css(): string
    {
        return <<<'CSS'
<style>
.ev-wrap{display:flex;flex-direction:column;gap:8px;padding:8px 0;min-width:340px;max-width:520px;white-space:normal}
.ev-top{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.ev-status{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:999px;font-size:10.5px;font-weight:700;letter-spacing:.04em}
.ev-status.st-active{background:#ecfdf5;color:#047857}
.ev-status.st-active .ev-dot{background:#10b981}
.ev-status.st-completed{background:#f1f5f9;color:#475569}
.ev-status.st-completed .ev-dot{background:#94a3b8}
.ev-status.st-draft{background:#fffbeb;color:#b45309}
.ev-status.st-draft .ev-dot{background:#f59e0b}
.ev-status.st-cancelled{background:#fef2f2;color:#b91c1c}
.ev-status.st-cancelled .ev-dot{background:#ef4444}
.ev-dot{width:6px;height:6px;border-radius:50%;display:inline-block}
.ev-dot.pulse{animation:evpulse 1.6s infinite}
@keyframes evpulse{0%{box-shadow:0 0 0 0 rgba(16,185,129,.6)}70%{box-shadow:0 0 0 6px rgba(16,185,129,0)}100%{box-shadow:0 0 0 0 rgba(16,185,129,0)}}
.ev-cat{font-size:10.5px;font-weight:600;color:#4338ca;background:#eef2ff;border:1px solid #e0e7ff;padding:3px 10px;border-radius:999px}
.ev-year{font-size:10.5px;font-weight:600;color:#64748b;background:#f1f5f9;padding:3px 8px;border-radius:999px}
.ev-title{font-size:14.5px;font-weight:700;color:#0f172a;line-height:1.35;letter-spacing:-.01em}
.ev-stats{display:flex;gap:14px;flex-wrap:wrap;font-size:11.5px;color:#64748b}
.ev-stats b{color:#4f46e5;font-weight:700}
.ev-loc-list{display:flex;flex-direction:column;gap:8px;border-top:1px dashed #e2e8f0;padding-top:8px}
.ev-loc{display:flex;flex-direction:column;gap:5px}
.ev-loc-head{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:600;color:#1e293b}
.ev-loc-pin{width:18px;height:18px;border-radius:6px;background:#fef2f2;color:#ef4444;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
.ev-loc-meta{font-size:11px;color:#94a3b8;font-weight:500}
.ev-chips{display:flex;flex-wrap:wrap;gap:4px;padding-left:25px}
.ev-chip{font-size:10.5px;padding:2px 8px;border-radius:6px;background:#f8fafc;border:1px solid #e2e8f0;color:#475569;max-width:230px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ev-chip b{color:#4f46e5;font-weight:700;margin-left:2px}
.ev-chip.more{background:#eef2ff;border-color:#c7d2fe;color:#4338ca;font-weight:600;cursor:help}
.ev-empty{font-size:11px;color:#94a3b8;font-style:italic}

.ev-date{display:flex;gap:10px;align-items:center;min-width:180px;padding:8px 0}
.ev-cal{width:46px;border-radius:10px;overflow:hidden;border:1px solid #e2e8f0;text-align:center;background:#fff;box-shadow:0 1px 2px rgba(15,23,42,.06);flex-shrink:0}
.ev-cal-m{font-size:9.5px;font-weight:800;color:#fff;padding:2px 0;text-transform:uppercase;letter-spacing:.06em}
.ev-cal-d{font-size:17px;font-weight:800;color:#0f172a;padding:3px 0 4px;line-height:1.1}
.ev-range{font-size:12px;font-weight:600;color:#0f172a;white-space:nowrap}
.ev-sub{font-size:11px;color:#64748b;margin-top:2px}
.ev-rel{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;margin-top:4px;padding:2px 8px;border-radius:999px}
.ev-rel.rel-active{background:#ecfdf5;color:#047857}
.ev-rel.rel-upcoming{background:#fffbeb;color:#b45309}
.ev-rel.rel-completed{background:#f1f5f9;color:#475569}

.ev-att{min-width:180px;display:flex;flex-direction:column;gap:6px;padding:8px 0}
.ev-att-top{display:flex;align-items:baseline;justify-content:space-between;gap:8px}
.ev-att-pct{font-size:18px;font-weight:800;line-height:1}
.ev-att-sesi{font-size:10.5px;color:#64748b;font-weight:600}
.ev-bar{height:6px;border-radius:999px;background:#f1f5f9;overflow:hidden}
.ev-bar>span{display:block;height:100%;border-radius:999px}
.ev-att-foot{display:flex;gap:12px;font-size:11px;color:#64748b}
.ev-att-foot b{font-weight:700}

.ev-docs{min-width:160px;display:flex;flex-direction:column;gap:6px;padding:8px 0}
.ev-docs-top{display:flex;align-items:center;justify-content:space-between;font-size:11px;color:#64748b;font-weight:600}
.ev-docs-count{font-size:12px;font-weight:800}
.ev-doc-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:4px}
.ev-doc{display:flex;align-items:center;gap:4px;font-size:10.5px;font-weight:600;padding:3px 7px;border-radius:6px;white-space:nowrap}
.ev-doc.ok{background:#ecfdf5;color:#047857}
.ev-doc.no{background:transparent;color:#94a3b8;border:1px dashed #cbd5e1}

/* DARK MODE LUXURY PALETTE (MIDNIGHT SLATE & ELECTRIC INDIGO) */
.dark .ev-status.st-active{background:rgba(16,185,129,.15)!important;color:#34d399!important;border:1px solid rgba(16,185,129,.35)}
.dark .ev-status.st-active .ev-dot{background:#10b981!important}
.dark .ev-status.st-completed{background:rgba(255,255,255,.06)!important;color:#94a3b8!important;border:1px solid rgba(255,255,255,.1)}
.dark .ev-status.st-completed .ev-dot{background:#64748b!important}
.dark .ev-status.st-draft{background:rgba(245,158,11,.15)!important;color:#fbbf24!important;border:1px solid rgba(245,158,11,.3)}
.dark .ev-status.st-draft .ev-dot{background:#f59e0b!important}
.dark .ev-status.st-cancelled{background:rgba(239,68,68,.15)!important;color:#f87171!important;border:1px solid rgba(239,68,68,.3)}
.dark .ev-status.st-cancelled .ev-dot{background:#ef4444!important}

.dark .ev-title{color:#ffffff!important;font-weight:700}
.dark .ev-stats b{color:#818cf8!important}
.dark .ev-stats{color:#94a3b8!important}
.dark .ev-range{color:#ffffff!important}
.dark .ev-cal-d{color:#ffffff!important}
.dark .ev-cal{background:#0e1424!important;border-color:rgba(255,255,255,.1)!important;box-shadow:0 4px 12px rgba(0,0,0,.4)!important}
.dark .ev-loc-head{color:#e2e8f0!important}
.dark .ev-sub,.dark .ev-att-sesi,.dark .ev-att-foot,.dark .ev-docs-top{color:#94a3b8!important}
.dark .ev-cat{background:rgba(99,102,241,.15)!important;color:#a5b4fc!important;border:1px solid rgba(99,102,241,.3)!important}
.dark .ev-year{background:rgba(255,255,255,.06)!important;color:#94a3b8!important}
.dark .ev-loc-list{border-top-color:rgba(255,255,255,.08)!important}
.dark .ev-loc-pin{background:rgba(239,68,68,.15)!important}
.dark .ev-chip{background:#121a2d!important;border-color:rgba(255,255,255,.08)!important;color:#cbd5e1!important}
.dark .ev-chip b{color:#818cf8!important}
.dark .ev-chip.more{background:rgba(99,102,241,.15)!important;border-color:rgba(99,102,241,.35)!important;color:#a5b4fc!important}
.dark .ev-bar{background:#1e293b!important}
.dark .ev-doc.ok{background:rgba(16,185,129,.15)!important;color:#34d399!important;border:1px solid rgba(16,185,129,.3)!important}
.dark .ev-doc.no{border-color:rgba(255,255,255,.12)!important;color:#64748b!important}
.dark .ev-rel.rel-active{background:rgba(16,185,129,.15)!important;color:#34d399!important;border:1px solid rgba(16,185,129,.3)!important}
.dark .ev-rel.rel-upcoming{background:rgba(245,158,11,.15)!important;color:#fbbf24!important;border:1px solid rgba(245,158,11,.3)!important}
.dark .ev-rel.rel-completed{background:rgba(255,255,255,.06)!important;color:#94a3b8!important;border:1px solid rgba(255,255,255,.1)!important}
</style>
CSS;
    }

    protected static function eventColumn(): TextColumn
    {
        return TextColumn::make('name')
            ->label('Kegiatan & Titik Lokasi')
            ->searchable(['name'])
            ->sortable()
            ->html()
            ->formatStateUsing(function (Event $record): HtmlString {
                $statusKey = $record->status ?? 'draft';
                $statusConfig = [
                    'active' => ['Berlangsung', true],
                    'completed' => ['Selesai', false],
                    'cancelled' => ['Dibatalkan', false],
                ];
                [$sLabel, $pulse] = $statusConfig[$statusKey] ?? ['Draft', false];
                $statusPill = "<span class='ev-status st-{$statusKey}'>"
                    . "<span class='ev-dot" . ($pulse ? ' pulse' : '') . "'></span>{$sLabel}</span>";

                $type = $record->procurementType?->name;
                $catPill = $type ? "<span class='ev-cat'>" . e($type) . "</span>" : '';
                $yearPill = $record->formation_year ? "<span class='ev-year'>Formasi " . e($record->formation_year) . "</span>" : '';

                $locations = $record->eventLocations;
                $totalPeserta = 0;
                $totalInstansi = 0;
                $locHtml = '';

                foreach ($locations as $el) {
                    $institutions = $el->eventLocationInstitutions;
                    $locPeserta = (int) $institutions->sum('participants_count');
                    $totalPeserta += $locPeserta;
                    $totalInstansi += $institutions->count();

                    $chips = '';
                    foreach ($institutions->take(4) as $eli) {
                        $iName = e($eli->institution?->name ?? '-');
                        $chips .= "<span class='ev-chip' title='{$iName}'>{$iName}<b>" . number_format((int) $eli->participants_count) . "</b></span>";
                    }
                    if ($institutions->count() > 4) {
                        $rest = $institutions->slice(4)
                            ->map(fn ($eli) => ($eli->institution?->name ?? '-') . ' (' . (int) $eli->participants_count . ')')
                            ->implode('&#10;');
                        $chips .= "<span class='ev-chip more' title='" . e($rest) . "'>+" . ($institutions->count() - 4) . " instansi lainnya</span>";
                    }

                    $city = $el->location?->city ? ' · ' . e($el->location->city) : '';
                    $locHtml .= "
                        <div class='ev-loc'>
                            <div class='ev-loc-head'>
                                <span class='ev-loc-pin'><svg width='11' height='11' viewBox='0 0 24 24' fill='currentColor'><path d='M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z'/></svg></span>
                                <span>" . e($el->location?->name ?? '-') . "</span>
                                <span class='ev-loc-meta'>{$city} · " . number_format($locPeserta) . " peserta</span>
                            </div>
                            " . ($chips ? "<div class='ev-chips'>{$chips}</div>" : '') . "
                        </div>";
                }

                if ($locations->isEmpty()) {
                    $locHtml = "<div class='ev-empty'>Belum ada titik lokasi</div>";
                }

                $stats = "
                    <div class='ev-stats'>
                        <span><b>" . $locations->count() . "</b> titik lokasi</span>
                        <span><b>{$totalInstansi}</b> instansi</span>
                        <span><b>" . number_format($totalPeserta) . "</b> peserta</span>
                    </div>";

                return new HtmlString(self::css() . "
                    <div class='ev-wrap'>
                        <div class='ev-top'>{$statusPill}{$catPill}{$yearPill}</div>
                        <div class='ev-title'>" . e($record->name) . "</div>
                        {$stats}
                        <div class='ev-loc-list'>{$locHtml}</div>
                    </div>");
            });
    }

    protected static function scheduleColumn(): TextColumn
    {
        return TextColumn::make('start_date')
            ->label('Jadwal')
            ->sortable()
            ->html()
            ->getStateUsing(fn (Event $record) => $record->id)
            ->formatStateUsing(function (Event $record): HtmlString {
                if (! $record->start_date || ! $record->end_date) {
                    return new HtmlString("<div class='ev-date'><span class='ev-sub' style='font-style:italic;'>Belum dijadwalkan</span></div>");
                }

                $start = Carbon::parse($record->start_date)->startOfDay();
                $end = Carbon::parse($record->end_date)->startOfDay();
                $now = now()->startOfDay();
                $duration = (int) $start->diffInDays($end) + 1;

                if ($now->between($start, $end)) {
                    [$color, $relClass, $rel] = ['#10b981', 'rel-active', 'Sedang berlangsung'];
                } elseif ($now->lt($start)) {
                    $days = (int) abs($now->diffInDays($start));
                    [$color, $relClass, $rel] = ['#f59e0b', 'rel-upcoming', "Mulai {$days} hari lagi"];
                } else {
                    $days = (int) abs($end->diffInDays($now));
                    [$color, $relClass, $rel] = ['#64748b', 'rel-completed', "Selesai {$days} hari lalu"];
                }

                $range = $start->isSameDay($end)
                    ? $start->translatedFormat('d M Y')
                    : $start->translatedFormat('d M') . ' – ' . $end->translatedFormat('d M Y');

                return new HtmlString("
                    <div class='ev-date'>
                        <div class='ev-cal'>
                            <div class='ev-cal-m' style='background:{$color};'>" . $start->translatedFormat('M') . "</div>
                            <div class='ev-cal-d'>" . $start->format('d') . "</div>
                        </div>
                        <div>
                            <div class='ev-range'>{$range}</div>
                            <div class='ev-sub'>{$duration} hari pelaksanaan</div>
                            <span class='ev-rel {$relClass}'>{$rel}</span>
                        </div>
                    </div>");
            });
    }

    protected static function attendanceColumn(): TextColumn
    {
        return TextColumn::make('laporan_kehadiran')
            ->label('Kehadiran')
            ->html()
            ->getStateUsing(fn (Event $record) => $record->reports->count())
            ->formatStateUsing(function (Event $record, $state): HtmlString {
                $sesi = (int) $state;
                if ($sesi === 0) {
                    return new HtmlString("
                        <div class='ev-att'>
                            <span class='ev-att-sesi'>Belum ada laporan sesi</span>
                            <div class='ev-bar'><span style='width:0;'></span></div>
                        </div>");
                }

                $hadir = (int) $record->reports->sum('present_count');
                $absen = (int) $record->reports->sum('absent_count');
                $total = (int) $record->reports->sum('total_participants') ?: ($hadir + $absen);
                $pct = $total > 0 ? round($hadir / $total * 100, 1) : 0;
                $color = $pct >= 90 ? '#10b981' : ($pct >= 75 ? '#f59e0b' : '#ef4444');

                return new HtmlString("
                    <div class='ev-att'>
                        <div class='ev-att-top'>
                            <span class='ev-att-pct' style='color:{$color};'>{$pct}%</span>
                            <span class='ev-att-sesi'>{$sesi} sesi</span>
                        </div>
                        <div class='ev-bar'><span style='width:" . min($pct, 100) . "%;background:{$color};'></span></div>
                        <div class='ev-att-foot'>
                            <span><b style='color:#10b981;'>" . number_format($hadir) . "</b> hadir</span>
                            <span><b style='color:#ef4444;'>" . number_format($absen) . "</b> absen</span>
                        </div>
                    </div>");
            });
    }

    protected static function documentColumn(): TextColumn
    {
        return TextColumn::make('status_dokumen')
            ->label('Dokumen')
            ->html()
            ->getStateUsing(fn (Event $record): int => collect([
                $record->doc_implementation_report,
                $record->doc_team_decree,
                $record->doc_ba_catos,
                $record->doc_institution_announcement,
            ])->filter()->count())
            ->formatStateUsing(function (Event $record, $state): HtmlString {
                $docs = [
                    'Laporan' => $record->doc_implementation_report,
                    'SK Tim' => $record->doc_team_decree,
                    'BA CATOS' => $record->doc_ba_catos,
                    'Pengumuman' => $record->doc_institution_announcement,
                ];
                $count = (int) $state;
                $countColor = $count === 4 ? '#10b981' : ($count === 0 ? '#94a3b8' : '#f59e0b');

                $grid = '';
                foreach ($docs as $label => $file) {
                    $grid .= $file
                        ? "<span class='ev-doc ok'>✓ {$label}</span>"
                        : "<span class='ev-doc no'>○ {$label}</span>";
                }

                return new HtmlString("
                    <div class='ev-docs'>
                        <div class='ev-docs-top'>
                            <span>Kelengkapan</span>
                            <span class='ev-docs-count' style='color:{$countColor};'>{$count}/4</span>
                        </div>
                        <div class='ev-doc-grid'>{$grid}</div>
                    </div>");
            })
            ->sortable();
    }
}
