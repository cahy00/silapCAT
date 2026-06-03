<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ProcurementTrendChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 4;
    protected ?string $heading = 'Kegiatan Berdasarkan Jenis Pengadaan';

    protected function getFilters(): ?array
    {
        $years = \App\Models\Event::query()
            ->whereNotNull('formation_year')
            ->distinct()
            ->orderBy('formation_year', 'desc')
            ->pluck('formation_year', 'formation_year')
            ->toArray();

        return [
            null => 'Semua Tahun',
        ] + $years;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = \App\Models\Event::query()
            ->when($activeFilter, fn ($query) => $query->where('formation_year', $activeFilter))
            ->join('procurement_types', 'events.procurement_type_id', '=', 'procurement_types.id')
            ->select('procurement_types.name', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('procurement_types.name')
            ->pluck('count', 'name')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kegiatan',
                    'data' => array_values($data),
                    'backgroundColor' => '#6366f1',
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
