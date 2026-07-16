<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ExamScore;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class AverageScoreByEventChart extends ChartWidget
{
    protected ?string $heading = 'Rata-rata Nilai per Kegiatan';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $events = Event::with(['examScores'])
            ->has('examScores')
            ->latest('id')
            ->take(5)
            ->get();

        $labels = [];
        $avgCat = [];
        $avgInterview = [];
        $avgTotal = [];

        foreach ($events->reverse() as $event) {
            $labels[] = str($event->name)->limit(15);
            $avgCat[] = round($event->examScores->avg('cat_score'), 2);
            $avgInterview[] = round($event->examScores->avg('interview_score'), 2);
            $avgTotal[] = round($event->examScores->avg('total_score'), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata CAT',
                    'data' => $avgCat,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Rata-rata Wawancara',
                    'data' => $avgInterview,
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Rata-rata Total',
                    'data' => $avgTotal,
                    'backgroundColor' => '#10b981',
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
