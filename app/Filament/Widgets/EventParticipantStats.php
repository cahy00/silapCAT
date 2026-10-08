<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\ExamScore;
use App\Models\Report;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventParticipantStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $year = $this->filters['formation_year'] ?? null;
        $eventId = $this->filters['event_id'] ?? null;

        $cacheKey = 'event_part_stats_' . md5(json_encode([$year, $eventId]));

        $data = Cache::remember($cacheKey, 60, function () use ($year, $eventId) {
            $eventIds = null;
            if ($eventId) {
                $eventIds = [(int) $eventId];
            } elseif ($year) {
                $eventIds = Event::where('formation_year', $year)->pluck('id')->toArray();
            }

            $examQuery = ExamScore::query();
            if ($eventIds !== null) {
                $examQuery->whereIn('event_id', $eventIds);
            }

            $passedExam = (clone $examQuery)->where('status', 'Lulus')->count();
            $failedExam = (clone $examQuery)->where('status', 'Tidak Lulus')->count();

            $reportQuery = Report::query();
            if ($eventIds !== null) {
                $reportQuery->whereHas('eventLocation', fn ($q) => $q->whereIn('event_id', $eventIds));
            }

            $totalPresent = (int) ($reportQuery->sum('present_count') ?? 0);
            $totalAbsent = (int) ($reportQuery->sum('absent_count') ?? 0);

            return compact('passedExam', 'failedExam', 'totalPresent', 'totalAbsent');
        });

        return [
            Stat::make('Total Peserta Lulus', $data['passedExam'])
                ->description('Peserta yang lulus seleksi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Peserta Tidak Lulus', $data['failedExam'])
                ->description('Peserta yang tidak lulus seleksi')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            Stat::make('Total Kehadiran', $data['totalPresent'])
                ->description('Total kehadiran tercatat di laporan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Total Ketidakhadiran', $data['totalAbsent'])
                ->description('Total ketidakhadiran tercatat di laporan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('warning'),
        ];
    }
}
