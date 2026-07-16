<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ExamScore;
use App\Models\Report;

class EventParticipantStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $passedExam = ExamScore::where('status', 'Lulus')->count();
        $failedExam = ExamScore::where('status', 'Tidak Lulus')->count();
        
        $totalPresent = Report::sum('present_count') ?? 0;
        $totalAbsent = Report::sum('absent_count') ?? 0;

        return [
            Stat::make('Total Peserta Lulus', $passedExam)
                ->description('Peserta yang lulus ujian')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Peserta Tidak Lulus', $failedExam)
                ->description('Peserta yang tidak lulus ujian')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            Stat::make('Total Kehadiran', $totalPresent)
                ->description('Total kehadiran tercatat di laporan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Total Ketidakhadiran', $totalAbsent)
                ->description('Total ketidakhadiran tercatat di laporan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('warning'),
        ];
    }
}
