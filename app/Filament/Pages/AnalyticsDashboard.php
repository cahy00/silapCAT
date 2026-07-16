<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ExamScorePassFailChart;
use App\Filament\Widgets\AverageScoreByEventChart;
use App\Filament\Widgets\ReportParticipantChart;
use App\Filament\Widgets\EventParticipantStats;

class AnalyticsDashboard extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Analitik';
    protected static ?string $title = 'Dashboard Analitik';

    protected function getHeaderWidgets(): array
    {
        return [
            EventParticipantStats::class,
            ExamScorePassFailChart::class,
            ReportParticipantChart::class,
            AverageScoreByEventChart::class,
        ];
    }
}
