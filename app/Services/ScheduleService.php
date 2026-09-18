<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventLocation;
use App\Models\EventLocationInstitution;
use App\Models\Institution;
use App\Models\Location;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Calculate schedule for multiple institution items sharing the same location,
     * ensuring daily & session PC capacity limits are respected across instansi.
     */
    public function calculateGroupedLocationSchedule(array $items): array
    {
        // Track overall session usage for this location: $usage[date_string][session_key] = used_count
        $locationUsage = [];

        $results = [];

        foreach ($items as $item) {
            $pcCapacity = max((int) ($item['pc_capacity'] ?? 0), 1);
            $sessionsCount = max((int) ($item['sessions_per_day'] ?? 4), 1);
            $totalParticipants = max((int) ($item['participants_count'] ?? 0), 0);
            $hasOpeningDay = !empty($item['has_opening_day']);
            $holidayDates = array_filter($item['holiday_dates'] ?? []);

            $startDateStr = $item['start_date'] ?? now()->format('Y-m-d');
            $currentDate = Carbon::parse($startDateStr);

            if ($hasOpeningDay) {
                $openingDate = $currentDate->copy();
                $currentDate->addDay();
            } else {
                $openingDate = null;
            }

            $remainingParticipants = $totalParticipants;
            $examDaysList = [];
            $currentDayNumber = 1;

            while ($remainingParticipants > 0) {
                $dateString = $currentDate->format('Y-m-d');

                // Skip Sunday (tidak ada pelaksanaan) or holidays
                if ($currentDate->isSunday() || in_array($dateString, $holidayDates)) {
                    $currentDate->addDay();
                    continue;
                }

                // Hari Jumat maksimal 2 sesi (waktu terbatas karena shalat Jumat)
                $actualSessionsToday = $currentDate->isFriday() ? min(2, $sessionsCount) : $sessionsCount;

                if (!isset($locationUsage[$dateString])) {
                    $locationUsage[$dateString] = [];
                    for ($s = 1; $s <= $actualSessionsToday; $s++) {
                        $locationUsage[$dateString]["session_$s"] = 0;
                    }
                }

                $sessionDistribution = [];
                $dayTotal = 0;

                for ($s = 1; $s <= $actualSessionsToday; $s++) {
                    $sessionKey = "session_$s";
                    $alreadyUsed = $locationUsage[$dateString][$sessionKey] ?? 0;
                    $availableSpace = max(0, $pcCapacity - $alreadyUsed);

                    if ($availableSpace > 0 && $remainingParticipants > 0) {
                        $allocated = min($remainingParticipants, $availableSpace);
                        $sessionDistribution[$sessionKey] = $allocated;
                        $locationUsage[$dateString][$sessionKey] += $allocated;
                        $remainingParticipants -= $allocated;
                        $dayTotal += $allocated;
                    } else {
                        $sessionDistribution[$sessionKey] = 0;
                    }
                }

                if ($dayTotal > 0) {
                    $examDaysList[] = [
                        'day_number' => $currentDayNumber,
                        'date' => $dateString,
                        'day_total' => $dayTotal,
                        'sessions' => $sessionDistribution,
                    ];
                    $currentDayNumber++;
                }

                if ($remainingParticipants > 0) {
                    $currentDate->addDay();
                }
            }

            $endDateStr = $currentDate->format('Y-m-d');
            $totalExamDays = count($examDaysList);
            $dailyCapacity = $pcCapacity * $sessionsCount;

            $results[] = [
                'index' => $item['index'] ?? null,
                'calculation' => [
                    'pc_capacity' => $pcCapacity,
                    'sessions_per_day' => $sessionsCount,
                    'daily_capacity' => $dailyCapacity,
                    'total_participants' => $totalParticipants,
                    'total_exam_days' => $totalExamDays,
                    'start_date' => $startDateStr,
                    'end_date' => $endDateStr,
                    'has_opening_day' => $hasOpeningDay,
                    'opening_date' => $openingDate ? $openingDate->format('Y-m-d') : null,
                    'days' => $examDaysList,
                ],
            ];
        }

        return $results;
    }

    /**
     * Calculate schedule details for a single location item input.
     */
    public function calculateLocationSchedule(array $data): array
    {
        $pcCapacity = max((int) ($data['pc_capacity'] ?? 0), 1);
        $sessionsCount = max((int) ($data['sessions_per_day'] ?? 4), 1);
        $totalParticipants = max((int) ($data['participants_count'] ?? 0), 0);
        $hasOpeningDay = !empty($data['has_opening_day']);
        $holidayDates = array_filter($data['holiday_dates'] ?? []);

        $startDateStr = $data['start_date'] ?? now()->format('Y-m-d');
        $currentDate = Carbon::parse($startDateStr);

        $examDaysList = [];
        $currentDayNumber = 1;
        $remainingParticipants = $totalParticipants;

        // If opening day is enabled, day 0 is opening day
        if ($hasOpeningDay) {
            $openingDate = $currentDate->copy();
            $currentDate->addDay();
        } else {
            $openingDate = null;
        }

        while ($remainingParticipants > 0) {
            $dateString = $currentDate->format('Y-m-d');

            // Skip Sunday (tidak ada pelaksanaan) or holidays
            if ($currentDate->isSunday() || in_array($dateString, $holidayDates)) {
                $currentDate->addDay();
                continue;
            }

            // Hari Jumat maksimal 2 sesi (waktu terbatas karena shalat Jumat)
            $actualSessionsToday = $currentDate->isFriday() ? min(2, $sessionsCount) : $sessionsCount;
            $dayCapacity = $pcCapacity * $actualSessionsToday;
            $dayTotal = min($remainingParticipants, $dayCapacity);

            // Calculate per session
            $sessionDistribution = [];
            $remainingForDay = $dayTotal;
            for ($s = 1; $s <= $actualSessionsToday; $s++) {
                $sessionQuota = min($remainingForDay, $pcCapacity);
                $sessionDistribution["session_$s"] = $sessionQuota;
                $remainingForDay -= $sessionQuota;
            }

            $examDaysList[] = [
                'day_number' => $currentDayNumber,
                'date' => $dateString,
                'day_total' => $dayTotal,
                'sessions' => $sessionDistribution,
            ];

            $remainingParticipants -= $dayTotal;
            $currentDayNumber++;

            if ($remainingParticipants > 0) {
                $currentDate->addDay();
            }
        }

        $endDateStr = $currentDate->format('Y-m-d');
        $totalExamDays = count($examDaysList);
        $dailyCapacity = $pcCapacity * $sessionsCount;

        return [
            'pc_capacity' => $pcCapacity,
            'sessions_per_day' => $sessionsCount,
            'daily_capacity' => $dailyCapacity,
            'total_participants' => $totalParticipants,
            'total_exam_days' => $totalExamDays,
            'start_date' => $startDateStr,
            'end_date' => $endDateStr,
            'has_opening_day' => $hasOpeningDay,
            'opening_date' => $openingDate ? $openingDate->format('Y-m-d') : null,
            'days' => $examDaysList,
        ];
    }

    /**
     * Store scheduling items into primary Event, EventLocation, and EventLocationInstitution tables.
     * Groups by location_id and creates 1 dedicated Event per 1 Titik Lokasi.
     *
     * @return Event[] Array of created Event models
     */
    public function saveEventWithSchedules(array $eventData, array $itemsData): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($eventData, $itemsData) {
            // 1. Group items by location_id
            $locationsGrouped = [];
            foreach ($itemsData as $item) {
                $locationId = $item['location_id'];
                if (!isset($locationsGrouped[$locationId])) {
                    $locationsGrouped[$locationId] = [
                        'location_id' => $locationId,
                        'start_date' => $item['start_date'] ?? null,
                        'end_date' => $item['end_date'] ?? null,
                        'session_type' => ($item['sessions_per_day'] ?? 4) . '_sessions',
                        'has_opening_day' => !empty($item['has_opening_day']),
                        'holiday_dates' => $item['holiday_dates'] ?? [],
                        'institutions' => [],
                        'total_participants' => 0,
                    ];
                }

                $locationsGrouped[$locationId]['total_participants'] += (int) ($item['participants_count'] ?? 0);
                if ($item['start_date'] && ($locationsGrouped[$locationId]['start_date'] === null || $item['start_date'] < $locationsGrouped[$locationId]['start_date'])) {
                    $locationsGrouped[$locationId]['start_date'] = $item['start_date'];
                }
                if ($item['end_date'] && ($locationsGrouped[$locationId]['end_date'] === null || $item['end_date'] > $locationsGrouped[$locationId]['end_date'])) {
                    $locationsGrouped[$locationId]['end_date'] = $item['end_date'];
                }

                $officerIds = $item['officer_ids'] ?? array_merge(
                    (array) ($item['koordinator_ids'] ?? ($item['koordinator_id'] ? [$item['koordinator_id']] : [])),
                    (array) ($item['it_ids'] ?? ($item['it_id'] ? [$item['it_id']] : [])),
                    (array) ($item['pengawas_ids'] ?? ($item['pengawas_id'] ? [$item['pengawas_id']] : []))
                );

                $locationsGrouped[$locationId]['institutions'][] = [
                    'institution_id' => $item['institution_id'],
                    'officer_ids' => array_values(array_filter((array) $officerIds)),
                    'koordinator_ids' => array_values(array_filter((array) ($item['koordinator_ids'] ?? ($item['koordinator_id'] ? [$item['koordinator_id']] : [])))),
                    'it_ids' => array_values(array_filter((array) ($item['it_ids'] ?? ($item['it_id'] ? [$item['it_id']] : [])))),
                    'pengawas_ids' => array_values(array_filter((array) ($item['pengawas_ids'] ?? ($item['pengawas_id'] ? [$item['pengawas_id']] : [])))),
                    'participants_count' => (int) ($item['participants_count'] ?? 0),
                ];
            }

            $createdEvents = [];
            $totalLocationsCount = count($locationsGrouped);

            // 2. Create 1 Event per 1 Tilok
            foreach ($locationsGrouped as $locData) {
                $location = Location::find($locData['location_id']);
                $locationName = $location?->name ?? 'Tilok #' . $locData['location_id'];

                // Append location name if there are multiple locations or to keep names clear and distinct
                $eventName = $totalLocationsCount > 1 
                    ? "{$eventData['name']} - {$locationName}"
                    : $eventData['name'];

                $event = Event::create([
                    'name' => $eventName,
                    'formation_year' => $eventData['formation_year'] ?? date('Y'),
                    'description' => $eventData['description'] ?? null,
                    'procurement_type_id' => $eventData['procurement_type_id'] ?? null,
                    'status' => $eventData['status'] ?? 'draft',
                    'start_date' => $locData['start_date'],
                    'end_date' => $locData['end_date'],
                ]);

                // Create EventLocation
                $eventLocation = EventLocation::create([
                    'event_id' => $event->id,
                    'location_id' => $locData['location_id'],
                    'participants_count' => $locData['total_participants'],
                    'start_date' => $locData['start_date'],
                    'end_date' => $locData['end_date'],
                    'session_type' => $locData['session_type'],
                    'has_opening_day' => $locData['has_opening_day'],
                    'holiday_dates' => $locData['holiday_dates'],
                ]);

                $officersWithRoles = []; // employee_id => [roles]

                foreach ($locData['institutions'] as $instData) {
                    EventLocationInstitution::create([
                        'event_location_id' => $eventLocation->id,
                        'institution_id' => $instData['institution_id'],
                        'participants_count' => $instData['participants_count'],
                    ]);

                    // Maintain relation in event_institutions
                    \App\Models\EventInstitution::firstOrCreate([
                        'event_id' => $event->id,
                        'institution_id' => $instData['institution_id'],
                    ]);

                    // Collect role-specific assignments
                    foreach ($instData['koordinator_ids'] ?? [] as $empId) {
                        if ($empId) $officersWithRoles[$empId][] = 'Koordinator';
                    }
                    foreach ($instData['it_ids'] ?? [] as $empId) {
                        if ($empId) $officersWithRoles[$empId][] = 'IT';
                    }
                    foreach ($instData['pengawas_ids'] ?? [] as $empId) {
                        if ($empId) $officersWithRoles[$empId][] = 'Pengawas';
                    }
                }

                // Save employees with their roles to event_employees
                foreach ($officersWithRoles as $empId => $roles) {
                    $uniqueRoles = array_values(array_unique(array_filter($roles)));
                    \App\Models\EventEmployee::create([
                        'event_id' => $event->id,
                        'employee_id' => $empId,
                        'role' => $uniqueRoles,
                    ]);
                }

                $createdEvents[] = $event;
            }

            return $createdEvents;
        });
    }
}
