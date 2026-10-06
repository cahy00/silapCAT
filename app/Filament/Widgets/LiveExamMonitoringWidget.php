<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Event;
use App\Models\EventLocation;
use Carbon\Carbon;

class LiveExamMonitoringWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.live-exam-monitoring';

    public function getActiveTiloksProperty()
    {
        $today = Carbon::today();

        // 1. Get events that are active or scheduled today
        $events = Event::with([
            'procurementType',
            'eventLocations.location.locationSurvey',
            'eventLocations.eventLocationInstitutions.institution',
            'eventInstitutions',
            'eventEmployees.employee',
            'reports',
        ])
        ->where('status', 'active')
        ->orWhere(function ($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->whereDate('end_date', '>=', $today);
        })
        ->orWhereHas('eventLocations', function ($lq) use ($today) {
            $lq->whereDate('start_date', '<=', $today)
               ->whereDate('end_date', '>=', $today);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // If no active events today, get 3 most recent active/draft events to display upcoming schedule
        if ($events->isEmpty()) {
            $events = Event::with([
                'procurementType',
                'eventLocations.location.locationSurvey',
                'eventLocations.eventLocationInstitutions.institution',
                'eventInstitutions',
                'eventEmployees.employee',
                'reports',
            ])
            ->whereIn('status', ['active', 'draft'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        }

        $cards = [];

        foreach ($events as $event) {
            $totalTarget = 0;
            foreach ($event->eventLocations as $el) {
                $totalTarget += (int) $el->eventLocationInstitutions->sum('participants_count');
            }
            if ($totalTarget === 0) {
                $totalTarget = (int) $event->eventInstitutions->sum('participants_count');
            }

            $present = (int) $event->reports->sum('present_count');
            $absent = (int) $event->reports->sum('absent_count');
            $attendanceRate = $totalTarget > 0 ? round(($present / $totalTarget) * 100, 1) : ($present > 0 ? 100 : 0);

            // Group staff
            $koordinators = [];
            $itStaff = [];
            $pengawas = [];

            foreach ($event->eventEmployees as $ee) {
                $empName = $ee->employee?->name ?? 'Petugas #' . $ee->employee_id;
                $roles = (array) $ee->role;
                if (in_array('Koordinator', $roles)) $koordinators[] = $empName;
                if (in_array('IT', $roles)) $itStaff[] = $empName;
                if (in_array('Pengawas', $roles)) $pengawas[] = $empName;
            }

            // Locations summary
            $locations = [];
            foreach ($event->eventLocations as $el) {
                $locName = $el->location?->name ?? 'Lokasi #' . $el->location_id;
                $locCity = $el->location?->city ?? '';
                $pcCount = $el->location?->locationSurvey?->pc_count ?? 0;
                $locations[] = [
                    'name' => $locName,
                    'city' => $locCity,
                    'pc_count' => $pcCount,
                    'dates' => ($el->start_date && $el->end_date)
                        ? $el->start_date->translatedFormat('d M') . ' - ' . $el->end_date->translatedFormat('d M Y')
                        : 'Jadwal fleksibel',
                ];
            }

            $cards[] = [
                'event_id' => $event->id,
                'name' => $event->name,
                'status' => $event->status,
                'is_live_today' => ($event->start_date && $event->end_date && $today->between($event->start_date, $event->end_date)) || $event->status === 'active',
                'procurement_type' => $event->procurementType?->name ?? 'Seleksi CAT',
                'formation_year' => $event->formation_year,
                'start_date' => $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-',
                'end_date' => $event->end_date ? $event->end_date->translatedFormat('d M Y') : '-',
                'total_target' => $totalTarget,
                'present' => $present,
                'absent' => $absent,
                'attendance_rate' => $attendanceRate,
                'locations' => $locations,
                'koordinators' => $koordinators,
                'it_staff' => $itStaff,
                'pengawas' => $pengawas,
                'reports_count' => $event->reports->count(),
            ];
        }

        return $cards;
    }
}
