<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\ExamScore;
use App\Models\Report;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class ExecutiveKpiWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $year = $this->filters['formation_year'] ?? null;
        $eventId = $this->filters['event_id'] ?? null;

        $cacheKey = 'exec_kpi_' . md5(json_encode([$year, $eventId]));

        $data = Cache::remember($cacheKey, 60, function () use ($year, $eventId) {
            $eventIds = null;
            if ($eventId) {
                $eventIds = [(int) $eventId];
            } elseif ($year) {
                $eventIds = Event::where('formation_year', $year)->pluck('id')->toArray();
            }

            // Metrik Peserta Terintegrasi (Opsi 1 & 2)
            $participantMetrics = \App\Support\ParticipantMetrics::aggregate($eventIds);

            // Report aggregation
            $repQuery = Report::query();
            if ($eventIds !== null) {
                $repQuery->whereHas('eventLocation', fn ($q) => $q->whereIn('event_id', $eventIds));
            }

            $maxReportScore = (float) ($repQuery->max('highest_score') ?? 0);
            $totalReports = (int) $repQuery->count();

            // ExamScore aggregation
            $examQuery = ExamScore::query();
            if ($eventIds !== null) {
                $examQuery->whereIn('event_id', $eventIds);
            }

            $avgScore = (float) ($examQuery->where('cat_score', '>', 0)->avg('cat_score') ?? 0);
            $maxExamScore = (float) ($examQuery->max('cat_score') ?? 0);
            $highestScore = max($maxReportScore, $maxExamScore);

            // Active events count
            $evQuery = Event::where('status', \App\Enums\EventStatus::Active->value);
            if ($year) {
                $evQuery->where('formation_year', $year);
            }
            if ($eventId) {
                $evQuery->where('id', $eventId);
            }
            $activeEvents = $evQuery->count();

            return compact('participantMetrics', 'highestScore', 'avgScore', 'totalReports', 'activeEvents');
        });

        $metrics = $data['participantMetrics'];
        $totalPresent = $metrics['present'];
        $totalParticipants = $metrics['target_quota'] ?: $metrics['effective_total'];
        $attendanceRate = $totalParticipants > 0 
            ? round(($totalPresent / $totalParticipants) * 100, 1) 
            : 0;

        $avgScore = $data['avgScore'];
        $highestScore = $data['highestScore'];
        $totalReports = $data['totalReports'];
        $activeEvents = $data['activeEvents'];

        return [
            Stat::make('Tingkat Kehadiran CAT', $attendanceRate . '%')
                ->description(number_format($totalPresent) . " hadir dari " . number_format($totalParticipants) . " total peserta")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->chart([70, 75, 80, 85, $attendanceRate])
                ->color($attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Rata-rata Skor Seleksi', $avgScore > 0 ? number_format($avgScore, 2, ',', '.') : '-')
                ->description('Rerata nilai ujian dari sesi terfilter')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([300, 340, 360, 380, $avgScore > 0 ? (int) $avgScore : 350])
                ->color('primary'),

            Stat::make('Skor Tertinggi Tercatat', $highestScore > 0 ? number_format($highestScore, 2, ',', '.') : '-')
                ->description('Nilai CAT tertinggi peserta seleksi')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Total Sesi & Berita Acara', number_format($totalReports) . ' Sesi')
                ->description("Terdokumentasi ({$activeEvents} event aktif)")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),
        ];
    }
}
