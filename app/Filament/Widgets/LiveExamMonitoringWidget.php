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

        // 1. Get events that are ongoing today or explicitly active
        $activeEvents = Event::with([
            'procurementType',
            'eventLocations.location.locationSurvey',
            'eventLocations.eventLocationInstitutions.institution',
            'eventInstitutions.institution',
            'eventEmployees.employee',
            'reports',
        ])
        ->where(function ($q) use ($today) {
            $q->where('status', 'active')
              ->orWhere('status', 'aktif')
              ->orWhere(function ($sub) use ($today) {
                  $sub->whereDate('start_date', '<=', $today)
                      ->whereDate('end_date', '>=', $today);
              })
              ->orWhereHas('eventLocations', function ($lq) use ($today) {
                  $lq->whereDate('start_date', '<=', $today)
                     ->whereDate('end_date', '>=', $today);
              });
        })
        ->orderBy('start_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        // If there are active events today, show them. Otherwise, show the most recent events (newest start_date first)
        if ($activeEvents->isNotEmpty()) {
            $events = $activeEvents;
        } else {
            $events = Event::with([
                'procurementType',
                'eventLocations.location.locationSurvey',
                'eventLocations.eventLocationInstitutions.institution',
                'eventInstitutions.institution',
                'eventEmployees.employee',
                'reports',
            ])
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
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

            // Locations and their institutions
            $locations = [];
            foreach ($event->eventLocations as $el) {
                $locName = $el->location?->name ?? 'Lokasi #' . $el->location_id;
                $locCity = $el->location?->city ?? '';
                $pcCount = $el->location?->locationSurvey?->pc_count ?? 0;

                $instList = [];
                foreach ($el->eventLocationInstitutions as $eli) {
                    if ($eli->institution) {
                        $instList[] = [
                            'name' => $eli->institution->name,
                            'participants_count' => (int) $eli->participants_count,
                        ];
                    }
                }

                $locations[] = [
                    'name' => $locName,
                    'city' => $locCity,
                    'pc_count' => $pcCount,
                    'institutions' => $instList,
                    'dates' => ($el->start_date && $el->end_date)
                        ? $el->start_date->translatedFormat('d M') . ' - ' . $el->end_date->translatedFormat('d M Y')
                        : 'Jadwal fleksibel',
                ];
            }

            // Fallback institutions if not assigned per location
            $allInstitutions = [];
            foreach ($event->eventInstitutions as $ei) {
                if ($ei->institution) {
                    $allInstitutions[] = [
                        'name' => $ei->institution->name,
                        'participants_count' => (int) $ei->participants_count,
                    ];
                }
            }

            $dateRange = null;
            if ($event->start_date && $event->end_date) {
                $dateRange = $event->start_date->translatedFormat('d M') . ' — ' . $event->end_date->translatedFormat('d M Y');
            } elseif ($event->start_date) {
                $dateRange = $event->start_date->translatedFormat('d M Y');
            }

            $cards[] = [
                'event_id' => $event->id,
                'name' => $event->name,
                'status' => $event->status,
                'is_live_today' => ($event->start_date && $event->end_date && $today->between($event->start_date, $event->end_date)) || $event->status === 'active',
                'procurement_type' => $event->procurementType?->name ?? 'Seleksi CAT',
                'formation_year' => $event->formation_year,
                'date_range' => $dateRange,
                'start_date' => $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-',
                'end_date' => $event->end_date ? $event->end_date->translatedFormat('d M Y') : '-',
                'total_target' => $totalTarget,
                'present' => $present,
                'absent' => $absent,
                'attendance_rate' => $attendanceRate,
                'locations' => $locations,
                'all_institutions' => $allInstitutions,
                'koordinators' => $koordinators,
                'it_staff' => $itStaff,
                'pengawas' => $pengawas,
                'reports_count' => $event->reports->count(),
            ];
        }

        return $cards;
    }
}
