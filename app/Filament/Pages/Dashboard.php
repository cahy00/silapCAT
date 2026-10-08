<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Models\Event;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getHeading(): string
    {
        return '';
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('formation_year')
                    ->label('Tahun Formasi')
                    ->placeholder('Semua Tahun')
                    ->options(function () {
                        return Event::query()
                            ->whereNotNull('formation_year')
                            ->distinct()
                            ->orderBy('formation_year', 'desc')
                            ->pluck('formation_year', 'formation_year')
                            ->toArray();
                    }),

                Select::make('event_id')
                    ->label('Kegiatan Tertentu')
                    ->placeholder('Semua Kegiatan')
                    ->searchable()
                    ->options(function () {
                        return Event::query()
                            ->orderBy('start_date', 'desc')
                            ->pluck('name', 'id')
                            ->toArray();
                    }),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeBanner::class,
            \App\Filament\Widgets\ExecutiveKpiWidget::class,
            \App\Filament\Widgets\LiveExamMonitoringWidget::class,
            \App\Filament\Widgets\ScoreDistributionMapWidget::class,
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
