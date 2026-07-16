<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Report;
use App\Models\Event;

class ReportParticipantChart extends ChartWidget
{
    protected ?string $heading = 'Tingkat Kehadiran per Kegiatan (Berdasarkan Laporan)';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Get reports grouped by event for the latest 5 events with reports
        $events = Event::withSum('reports', 'present_count')
            ->withSum('reports', 'absent_count')
            ->has('reports')
            ->latest('id')
            ->take(5)
            ->get();

        $labels = [];
        $present = [];
        $absent = [];

        foreach ($events->reverse() as $event) {
            $labels[] = str($event->name)->limit(15);
            $present[] = (int) $event->reports_sum_present_count;
            $absent[] = (int) $event->reports_sum_absent_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $present,
                    'backgroundColor' => '#10b981', // success
                ],
                [
                    'label' => 'Tidak Hadir',
                    'data' => $absent,
                    'backgroundColor' => '#ef4444', // danger
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
