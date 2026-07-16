<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ExamScore;
use Illuminate\Support\Facades\DB;

class ExamScorePassFailChart extends ChartWidget
{
    protected ?string $heading = 'Persentase Kelulusan Peserta (Exam Scores)';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = ExamScore::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

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
