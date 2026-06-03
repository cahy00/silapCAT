<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SurveyStatusChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 3;
    protected ?string $heading = 'Sebaran Status Kelayakan Survey';

    protected function getData(): array
    {
        $data = \App\Models\LocationSurvey::query()
            ->select('feasibility_status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('feasibility_status')
            ->pluck('count', 'feasibility_status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Status Survey',
                    'data' => [
                        $data['feasible'] ?? 0,
                        $data['not_feasible'] ?? 0,
                        $data['conditional'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#10b981', // success
                        '#ef4444', // danger
                        '#f59e0b', // warning
                    ],
                ],
            ],
            'labels' => ['Layak', 'Tidak Layak', 'Bersyarat'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
