<?php

namespace App\Filament\Resources\Reports\Widgets;

use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $query = Report::query();
        
        if (auth()->check() && auth()->user()->hasRole('operator')) {
            $query->where('user_id', auth()->id());
        }

        $totalParticipants = (clone $query)->sum('total_participants');
        $presentCount = (clone $query)->sum('present_count');
        $absentCount = (clone $query)->sum('absent_count');
        
        $highestScore = (clone $query)->max('highest_score') ?? 0;
        
        // Untuk nilai terendah, kita ambil nilai minimum yang lebih besar dari 0 atau not null
        $minQuery = (clone $query)->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
        $lowestScore = $minQuery->exists() ? $minQuery->min('lowest_score') : ((clone $query)->min('lowest_score') ?? 0);
        
        $totalSessions = (clone $query)->count();

        return [
            Stat::make('Total Peserta', number_format($totalParticipants))
                ->description('Akumulasi kuota peserta')
                ->descriptionIcon('heroicon-m-users')
                ->chart([10, 15, 12, 18, 14, 20, 18])
                ->color('primary'),
                
            Stat::make('Peserta Hadir', number_format($presentCount))
                ->description('Total kehadiran peserta')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([8, 12, 10, 16, 13, 18, 17])
                ->color('success'),
                
            Stat::make('Peserta Tidak Hadir', number_format($absentCount))
                ->description('Total peserta absen')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart([2, 3, 2, 2, 1, 2, 1])
                ->color('danger'),
                
            Stat::make('Nilai Tertinggi', number_format($highestScore))
                ->description('Skor CAT tertinggi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([400, 420, 450, 440, 460, 475, 480])
                ->color('info'),
                
            Stat::make('Nilai Terendah', number_format($lowestScore))
                ->description('Skor CAT terendah')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([200, 190, 210, 185, 205, 195, 200])
                ->color('warning'),
                
            Stat::make('Keseluruhan Sesi', number_format($totalSessions))
                ->description('Total sesi dilaporkan')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->color('gray'),
        ];
    }
}
