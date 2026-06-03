<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TopPcLocationsChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 5;
    protected ?string $heading = 'Top 5 Lokasi (Kapasitas PC Terbanyak)';

    protected function getData(): array
    {
        $data = \App\Models\Location::query()
            ->join('location_surveys', 'locations.id', '=', 'location_surveys.location_id')
            ->select('locations.name', 'location_surveys.pc_count')
            ->orderBy('location_surveys.pc_count', 'desc')
            ->limit(5)
            ->pluck('pc_count', 'name')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah PC',
                    'data' => array_values($data),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
