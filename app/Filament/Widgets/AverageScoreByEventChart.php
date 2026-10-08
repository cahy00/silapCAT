<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AverageScoreByEventChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Rata-rata Nilai per Kegiatan';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $year = $this->filters['formation_year'] ?? null;
        $eventId = $this->filters['event_id'] ?? null;

        $cacheKey = 'chart_avg_score_' . md5(json_encode([$year, $eventId]));

        return Cache::remember($cacheKey, 60, function () use ($year, $eventId) {
            $query = Event::query()
                ->join('exam_scores', 'events.id', '=', 'exam_scores.event_id')
                ->select(
                    'events.id',
                    'events.name',
                    DB::raw('AVG(exam_scores.cat_score) as avg_cat'),
                    DB::raw('AVG(exam_scores.interview_score) as avg_interview'),
                    DB::raw('AVG(exam_scores.total_score) as avg_total')
                )
                ->groupBy('events.id', 'events.name');

            if ($eventId) {
                $query->where('events.id', $eventId);
            } elseif ($year) {
                $query->where('events.formation_year', $year);
            }

            $events = $query->orderBy('events.id', 'desc')->take(5)->get();

            $labels = [];
            $avgCat = [];
            $avgInterview = [];
            $avgTotal = [];

            foreach ($events->reverse() as $event) {
                $labels[] = str($event->name)->limit(18);
                $avgCat[] = round((float) $event->avg_cat, 2);
                $avgInterview[] = round((float) $event->avg_interview, 2);
                $avgTotal[] = round((float) $event->avg_total, 2);
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
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
