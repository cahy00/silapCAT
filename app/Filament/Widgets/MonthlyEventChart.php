<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Event;
use Carbon\Carbon;

class MonthlyEventChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    protected ?string $heading = 'Grafik Rekapitulasi Kegiatan Per Bulan';

    protected function getFilters(): ?array
    {
        $years = Event::query()
            ->whereNotNull('formation_year')
            ->distinct()
            ->orderBy('formation_year', 'desc')
            ->pluck('formation_year', 'formation_year')
            ->toArray();

        $currentYear = now()->year;
        if (!isset($years[$currentYear])) {
            $years[$currentYear] = $currentYear;
        }

        ksort($years);

        return $years;
    }

    public ?string $filter = null;

    public function mount(): void
    {
        $this->filter = (string) now()->year;
    }

    protected function getData(): array
    {
        $activeYear = (int) ($this->filter ?? now()->year);
        $counts = array_fill(1, 12, 0);

        $events = Event::with('eventLocations')->get();

        foreach ($events as $event) {
            $dateStr = $event->start_date;
            if (!$dateStr && $event->eventLocations->isNotEmpty()) {
                $dateStr = $event->eventLocations->min('start_date');
            }
            if (!$dateStr) {
                $dateStr = $event->created_at;
            }

            if ($dateStr) {
                $date = Carbon::parse($dateStr);
                if ((int) $date->format('Y') === $activeYear) {
                    $month = (int) $date->format('n');
                    $counts[$month]++;
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kegiatan ' . $activeYear,
                    'data' => array_values($counts),
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#4f46e5',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
