<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ExamScore;
use App\Models\Report;
use App\Models\Event;
use App\Models\Institution;

class ExecutiveKpiWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalPresent = (int) (Report::sum('present_count') ?? 0);
        $totalAbsent = (int) (Report::sum('absent_count') ?? 0);
        $totalParticipants = $totalPresent + $totalAbsent;
        
        $attendanceRate = $totalParticipants > 0 
            ? round(($totalPresent / $totalParticipants) * 100, 1) 
            : 0;

        $avgScore = ExamScore::where('cat_score', '>', 0)->avg('cat_score');
        $highestScore = ExamScore::max('cat_score') ?? Report::max('highest_score') ?? 0;
        
        $totalReports = Report::count();
        $activeEvents = Event::where('status', 'active')->count();

        return [
            Stat::make('Tingkat Kehadiran CAT', $attendanceRate . '%')
                ->description("{$totalPresent} hadir dari {$totalParticipants} peserta")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->chart([70, 75, 80, 85, $attendanceRate])
                ->color($attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Rata-rata Skor Seleksi', $avgScore ? number_format($avgScore, 2, ',', '.') : '-')
                ->description('Rerata nilai ujian dari seluruh sesi')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([300, 340, 360, 380, $avgScore ? (int) $avgScore : 350])
                ->color('primary'),

            Stat::make('Skor Tertinggi Tercatat', $highestScore ? number_format($highestScore, 2, ',', '.') : '-')
                ->description('Nilai CAT tertinggi peserta seleksi')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Total Sesi & Berita Acara', number_format($totalReports) . ' Sesi')
                ->description("Terdokumentasi dalam {$activeEvents} event aktif")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),
        ];
    }
}
