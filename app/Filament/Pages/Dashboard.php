<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        return '';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\EventParticipantStats::class,
            \App\Filament\Widgets\ExamScorePassFailChart::class,
            \App\Filament\Widgets\ReportParticipantChart::class,
            \App\Filament\Widgets\AverageScoreByEventChart::class,
        ];
    }
}
