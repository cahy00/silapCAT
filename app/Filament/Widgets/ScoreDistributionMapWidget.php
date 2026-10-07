<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Event;
use App\Models\ExamScore;
use App\Models\Report;
use App\Models\Location;

class ScoreDistributionMapWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    protected string $view = 'filament.widgets.score-distribution-map';

    public function getScoreDistributionDataProperty(): array
    {
        // 1. Ambil data nilai ujian CAT (ExamScore)
        $scores = ExamScore::all();
        $totalScores = $scores->count();

        $passedCount = $scores->where('status', 'Lulus')->count();
        $failedCount = $scores->where('status', '!=', 'Lulus')->count();
        $passRate = $totalScores > 0 ? round(($passedCount / $totalScores) * 100, 1) : 0;
        $failRate = $totalScores > 0 ? round(($failedCount / $totalScores) * 100, 1) : 0;

        $avgCat = $totalScores > 0 ? round($scores->avg('cat_score') ?? 0, 2) : 0;
        $avgInterview = $totalScores > 0 ? round($scores->avg('interview_score') ?? 0, 2) : 0;
        $avgTotal = $totalScores > 0 ? round($scores->avg('total_score') ?? 0, 2) : 0;

        // Distribusi Rentang Skor CAT (standar terpusat: config/scoring.php)
        $passingGrade = \App\Support\ScoreBands::passingGrade();
        $bands = \App\Support\ScoreBands::distribute($scores->pluck('cat_score'), $passingGrade);

        // 2. Data Peta Sebaran & Statistik per Titik Lokasi
        $locations = Location::with([
            'locationSurvey',
            'eventLocations.event.reports',
            'eventLocations.eventLocationInstitutions',
        ])->get();

        $cityCoordinates = [
            'manokwari' => [-0.861453, 134.062042],
            'sorong' => [-0.876176, 131.255806],
            'fakfak' => [-2.926888, 132.296561],
            'fak-fak' => [-2.926888, 132.296561],
            'kaimana' => [-3.662283, 133.771973],
            'bintuni' => [-2.127814, 133.522644],
            'teluk bintuni' => [-2.127814, 133.522644],
            'wondama' => [-2.700000, 134.500000],
            'teluk wondama' => [-2.700000, 134.500000],
            'raja ampat' => [-0.463287, 130.824280],
            'waisai' => [-0.463287, 130.824280],
            'sorong selatan' => [-1.4988, 132.0163],
            'teminabuan' => [-1.4988, 132.0163],
            'maybrat' => [-1.3069, 132.4285],
            'tambrauw' => [-0.7600, 132.4000],
            'pegunungan arfak' => [-1.3800, 133.9100],
            'manokwari selatan' => [-1.5000, 134.1700],
            'ransiki' => [-1.5000, 134.1700],
            'jayapura' => [-2.53371, 140.71813],
            'merauke' => [-8.49911, 140.40138],
            'timika' => [-4.5467, 136.8837],
            'mimika' => [-4.5467, 136.8837],
            'biak' => [-1.1787, 136.0847],
            'nabire' => [-3.3664, 135.4967],
        ];

        $mapMarkers = [];
        $tilokRankings = [];

        foreach ($locations as $index => $loc) {
            $lat = $loc->latitude;
            $lng = $loc->longitude;

            if (empty($lat) || empty($lng)) {
                $cityClean = strtolower(trim($loc->city ?? ''));
                $nameClean = strtolower(trim($loc->name ?? ''));

                $foundCoord = null;
                foreach ($cityCoordinates as $key => $coord) {
                    if (str_contains($cityClean, $key) || str_contains($nameClean, $key)) {
                        $foundCoord = $coord;
                        break;
                    }
                }

                if ($foundCoord) {
                    $jitterLat = (($index % 5) - 2) * 0.008;
                    $jitterLng = ((($index * 2) % 5) - 2) * 0.008;
                    $lat = $foundCoord[0] + $jitterLat;
                    $lng = $foundCoord[1] + $jitterLng;
                } else {
                    $lat = -0.861453 + ($index * 0.005);
                    $lng = 134.062042 + ($index * 0.005);
                }
            }

            // Agregasi laporan ujian pada titik lokasi ini
            $reports = Report::whereHas('eventLocation', function ($q) use ($loc) {
                $q->where('location_id', $loc->id);
            })->get();

            $totalParticipants = (int) $reports->sum('total_participants');
            $presentCount = (int) $reports->sum('present_count');
            $absentCount = (int) $reports->sum('absent_count');
            $maxScore = (int) ($reports->max('highest_score') ?? 0);
            
            $validMinReports = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
            $minScore = $validMinReports->isNotEmpty() ? (int) $validMinReports->min('lowest_score') : 0;

            // Estimasi nilai rata-rata berbasis laporan sesi
            $weightedScoresSum = 0;
            $weightedCount = 0;
            foreach ($reports as $rep) {
                if ($rep->highest_score && $rep->present_count > 0) {
                    $sessionMid = ($rep->highest_score + ($rep->lowest_score ?? $rep->highest_score)) / 2;
                    $weightedScoresSum += ($sessionMid * $rep->present_count);
                    $weightedCount += $rep->present_count;
                }
            }
            $avgScore = $weightedCount > 0 ? round($weightedScoresSum / $weightedCount, 1) : 0;

            $tilokData = [
                'id' => $loc->id,
                'name' => $loc->name,
                'city' => $loc->city ?? 'Papua Barat',
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'pc_count' => $loc->locationSurvey?->pc_count ?? 0,
                'total_participants' => $totalParticipants,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'max_score' => $maxScore,
                'min_score' => $minScore,
                'avg_score' => $avgScore,
                'events_count' => $loc->eventLocations->count(),
            ];

            $mapMarkers[] = $tilokData;

            if ($presentCount > 0 || $avgScore > 0) {
                $tilokRankings[] = $tilokData;
            }
        }

        // Urutkan ranking berdasarkan rata-rata skor tertinggi
        usort($tilokRankings, fn ($a, $b) => $b['avg_score'] <=> $a['avg_score']);

        return [
            'total_scores' => $totalScores,
            'passed_count' => $passedCount,
            'failed_count' => $failedCount,
            'pass_rate' => $passRate,
            'fail_rate' => $failRate,
            'avg_cat' => $avgCat,
            'avg_interview' => $avgInterview,
            'avg_total' => $avgTotal,
            'bands' => $bands,
            'passing_grade' => $passingGrade,
            'range_a' => $bands[0]['count'] ?? 0,
            'range_b' => $bands[1]['count'] ?? 0,
            'range_c' => $bands[2]['count'] ?? 0,
            'range_d' => $bands[3]['count'] ?? 0,
            'range_e' => $bands[4]['count'] ?? 0,
            'pct_a' => $bands[0]['pct'] ?? 0,
            'pct_b' => $bands[1]['pct'] ?? 0,
            'pct_c' => $bands[2]['pct'] ?? 0,
            'pct_d' => $bands[3]['pct'] ?? 0,
            'pct_e' => $bands[4]['pct'] ?? 0,
            'map_markers' => $mapMarkers,
            'tilok_rankings' => $tilokRankings,
        ];
    }
}
