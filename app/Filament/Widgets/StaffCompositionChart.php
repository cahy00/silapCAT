<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class StaffCompositionChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 6;
    protected ?string $heading = 'Komposisi SDM Berdasarkan Peran';

    protected function getData(): array
    {
        $employees = \App\Models\Employee::all();
        $counts = [
            'Koordinator' => 0,
            'IT' => 0,
            'Pengawas' => 0,
        ];

        $employees = \App\Models\Employee::all();

        foreach ($employees as $employee) {
            $status = $employee->status ?? [];
            if (!is_array($status)) {
                $status = [$status]; // Handle if it was stored as string
            }

            foreach ($status as $s) {
                if (isset($counts[$s])) {
                    $counts[$s]++;
                } elseif ($s === 'coordinator' || str_contains($s, 'coordinator')) {
                    $counts['Koordinator']++;
                } elseif ($s === 'supervisor' || str_contains($s, 'supervisor')) {
                    $counts['Pengawas']++;
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pegawai',
                    'data' => [
                        $counts['Koordinator'],
                        $counts['IT'],
                        $counts['Pengawas'],
                    ],
                    'backgroundColor' => [
                        '#818cf8', // Indigo
                        '#34d399', // Emerald
                        '#fbbf24', // Amber
                    ],
                ],
            ],
            'labels' => ['Koordinator', 'Tim IT', 'Pengawas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
