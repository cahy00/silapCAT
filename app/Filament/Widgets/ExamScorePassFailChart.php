<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\ExamScore;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExamScorePassFailChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Persentase Kelulusan Peserta (Exam Scores)';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $year = $this->filters['formation_year'] ?? null;
        $eventId = $this->filters['event_id'] ?? null;

        $cacheKey = 'chart_pass_fail_' . md5(json_encode([$year, $eventId]));

        $data = Cache::remember($cacheKey, 60, function () use ($year, $eventId) {
            $query = ExamScore::query();

            if ($eventId) {
                $query->where('event_id', $eventId);
            } elseif ($year) {
                $query->whereHas('event', fn ($q) => $q->where('formation_year', $year));
            }

            return $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Status Kelulusan',
                    'data' => [
                        $data['Lulus'] ?? 0,
                        $data['Tidak Lulus'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#10b981', // success
                        '#ef4444', // danger
                    ],
                ],
            ],
            'labels' => ['Lulus', 'Tidak Lulus'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
