<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        return '';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeBanner::class,
            \App\Filament\Widgets\ExecutiveKpiWidget::class,
            \App\Filament\Widgets\LiveExamMonitoringWidget::class,
            \App\Filament\Widgets\LocationMapWidget::class,
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\EventParticipantStats::class,
            \App\Filament\Widgets\ExamScorePassFailChart::class,
            \App\Filament\Widgets\AverageScoreByEventChart::class,
            \App\Filament\Widgets\MonthlyEventChart::class,
            \App\Filament\Widgets\ProcurementTrendChart::class,
            \App\Filament\Widgets\StaffCompositionChart::class,
            \App\Filament\Widgets\SurveyStatusChart::class,
            \App\Filament\Widgets\TopPcLocationsChart::class,
            \App\Filament\Widgets\LatestEvents::class,
        ];
    }
}
