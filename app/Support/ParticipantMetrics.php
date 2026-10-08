<?php

namespace App\Support;

use App\Models\Event;
use App\Models\EventLocationInstitution;
use App\Models\EventInstitution;
use App\Models\Report;
use App\Models\ExamScore;

class ParticipantMetrics
{
    /**
     * Hitung metrik peserta untuk 1 Event spesifik secara hierarkis.
     * 
     * @param Event $event
     * @return array{target_quota: int, actual_registered: int, present: int, absent: int, attendance_rate: float, effective_total: int}
     */
    public static function forEvent(Event $event): array
    {
        // 1. Target Kuota Alokasi Awal (Perencanaan)
        $targetQuota = 0;
        if ($event->relationLoaded('eventLocations')) {
            foreach ($event->eventLocations as $el) {
                if ($el->relationLoaded('eventLocationInstitutions')) {
                    $targetQuota += (int) $el->eventLocationInstitutions->sum('participants_count');
                } else {
                    $targetQuota += (int) $el->eventLocationInstitutions()->sum('participants_count');
                }
            }
        } else {
            $targetQuota = (int) EventLocationInstitution::whereHas('eventLocation', fn ($q) => $q->where('event_id', $event->id))->sum('participants_count');
        }

        if ($targetQuota === 0) {
            $targetQuota = $event->relationLoaded('eventInstitutions')
                ? (int) $event->eventInstitutions->sum('participants_count')
                : (int) $event->eventInstitutions()->sum('participants_count');
        }

        // 2. Realisasi Kehadiran di Sesi Pelaksanaan (Berita Acara / Laporan)
        $reports = $event->relationLoaded('reports') ? $event->reports : $event->reports()->get();
        $present = (int) $reports->sum('present_count');
        $absent = (int) $reports->sum('absent_count');
        $actualFromReports = $present + $absent;

        // Jika tidak ada di reports tapi ada di exam_scores
        $examCount = $event->relationLoaded('examScores') ? $event->examScores->count() : $event->examScores()->count();
        $actualRegistered = $actualFromReports > 0 ? $actualFromReports : $examCount;

        // Target acuan untuk persentase kehadiran:
        // Jika target kuota terdata, gunakan target kuota. Jika tidak, gunakan realisasi laporan.
        $baseTarget = $targetQuota > 0 ? $targetQuota : $actualRegistered;
        $attendanceRate = $baseTarget > 0 ? round(($present / $baseTarget) * 100, 1) : ($present > 0 ? 100 : 0);

        // Effective total yang paling representatif untuk event ini
        $effectiveTotal = $actualRegistered > 0 ? $actualRegistered : $targetQuota;

        return [
            'target_quota' => $targetQuota,
            'actual_registered' => $actualRegistered,
            'present' => $present,
            'absent' => $absent,
            'attendance_rate' => $attendanceRate,
            'effective_total' => $effectiveTotal,
        ];
    }

    /**
     * Hitung agregasi metrik peserta global atau terfilter (misal untuk Dashboard / Resource Widget).
     * 
     * @param array|null $eventIds
     * @return array{target_quota: int, actual_registered: int, present: int, absent: int, attendance_rate: float, effective_total: int}
     */
    public static function aggregate(?array $eventIds = null): array
    {
        // Target Kuota (Perencanaan Titik Lokasi & Instansi)
        $eliQuery = EventLocationInstitution::query();
        if ($eventIds !== null) {
            $eliQuery->whereHas('eventLocation', fn ($q) => $q->whereIn('event_id', $eventIds));
        }
        $targetQuota = (int) ($eliQuery->sum('participants_count') ?? 0);

        if ($targetQuota === 0) {
            $eiQuery = EventInstitution::query();
            if ($eventIds !== null) {
                $eiQuery->whereIn('event_id', $eventIds);
            }
            $targetQuota = (int) ($eiQuery->sum('participants_count') ?? 0);
        }

        // Realisasi Sesi Ujian (Berita Acara)
        $repQuery = Report::query();
        if ($eventIds !== null) {
            $repQuery->whereHas('eventLocation', fn ($q) => $q->whereIn('event_id', $eventIds));
        }

        $present = (int) ($repQuery->sum('present_count') ?? 0);
        $absent = (int) ($repQuery->sum('absent_count') ?? 0);
        $actualRegistered = $present + $absent;

        // Base target acuan persentase
        $baseTarget = $targetQuota > 0 ? $targetQuota : $actualRegistered;
        $attendanceRate = $baseTarget > 0 ? round(($present / $baseTarget) * 100, 1) : ($present > 0 ? 100 : 0);

        $effectiveTotal = $actualRegistered > 0 ? $actualRegistered : $targetQuota;

        return [
            'target_quota' => $targetQuota,
            'actual_registered' => $actualRegistered,
            'present' => $present,
            'absent' => $absent,
            'attendance_rate' => $attendanceRate,
            'effective_total' => $effectiveTotal,
        ];
    }
}
