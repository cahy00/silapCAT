<?php

namespace App\Support;

use App\Models\Event;
use Illuminate\Support\Collection;

/**
 * Sumber tunggal standar kategori skor CAT & passing grade.
 */
class ScoreBands
{
    public static function passingGrade(?Event $event = null): float
    {
        $pg = $event?->procurementType?->passing_grade;

        return $pg !== null ? (float) $pg : (float) config('scoring.default_passing_grade', 250);
    }

    /**
     * Daftar kategori (urut menurun). Setiap item: key, min, max, label, icon, color, range_label.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function bands(?float $passingGrade = null): array
    {
        $pg = $passingGrade ?? (float) config('scoring.default_passing_grade', 250);

        $upper = collect(config('scoring.bands', []))
            ->filter(fn ($b) => $b['min'] > $pg)
            ->sortByDesc('min')
            ->values();

        $bands = [];
        $prevMin = null;
        foreach ($upper as $b) {
            $bands[] = $b + ['max' => $prevMin];
            $prevMin = $b['min'];
        }

        $bands[] = ['min' => $pg, 'max' => $prevMin, 'label' => 'Di Atas Passing Grade', 'icon' => '⚠️', 'color' => '#d97706'];
        $bands[] = ['min' => null, 'max' => $pg, 'label' => 'Di Bawah Passing Grade', 'icon' => '❌', 'color' => '#dc2626'];

        foreach ($bands as $i => &$b) {
            $b['key'] = chr(97 + $i);
            $b['range_label'] = self::rangeLabel($b['min'], $b['max']);
        }

        return $bands;
    }

    /**
     * Hitung jumlah & persentase per kategori dari koleksi nilai CAT.
     */
    public static function distribute(Collection $catScores, ?float $passingGrade = null): array
    {
        $scores = $catScores->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
        $total = $scores->count();

        return array_map(function ($b) use ($scores, $total) {
            $count = $scores->filter(function ($v) use ($b) {
                return ($b['min'] === null || $v >= $b['min']) && ($b['max'] === null || $v < $b['max']);
            })->count();

            return $b + ['count' => $count, 'pct' => $total > 0 ? round($count / $total * 100, 1) : 0];
        }, self::bands($passingGrade));
    }

    private static function rangeLabel($min, $max): string
    {
        $f = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');

        if ($min === null) {
            return '< '.$f($max);
        }
        if ($max === null) {
            return '≥ '.$f($min);
        }

        return $f($min).' – <'.$f($max);
    }
}
