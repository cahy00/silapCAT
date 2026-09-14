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

                if (in_array($dateString, $holidayDates)) {
                    $currentDate->addDay();
                    continue;
                }

                if (!isset($locationUsage[$dateString])) {
                    $locationUsage[$dateString] = [];
                    for ($s = 1; $s <= $sessionsCount; $s++) {
                        $locationUsage[$dateString]["session_$s"] = 0;
                    }
                }

                $sessionDistribution = [];
                $dayTotal = 0;

                for ($s = 1; $s <= $sessionsCount; $s++) {
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

        $dailyCapacity = $pcCapacity * $sessionsCount;
        $totalExamDays = $dailyCapacity > 0 ? (int) ceil($totalParticipants / $dailyCapacity) : 1;
        if ($totalExamDays < 1) {
            $totalExamDays = 1;
        }

        $startDateStr = $data['start_date'] ?? now()->format('Y-m-d');
        $currentDate = Carbon::parse($startDateStr);

        $examDaysList = [];
        $daysAdded = 0;

        // If opening day is enabled, day 0 is opening day
        if ($hasOpeningDay) {
            $openingDate = $currentDate->copy();
            $currentDate->addDay();
        } else {
            $openingDate = null;
        }

        while ($daysAdded < $totalExamDays) {
            $dateString = $currentDate->format('Y-m-d');
            if (in_array($dateString, $holidayDates)) {
                $currentDate->addDay();
                continue;
            }

            // Calculate participants for this day
            $remainingParticipants = $totalParticipants - array_sum(array_column($examDaysList, 'day_total'));
            $dayTotal = min($remainingParticipants, $dailyCapacity);

            // Calculate per session
            $sessionDistribution = [];
            $remainingForDay = $dayTotal;
            for ($s = 1; $s <= $sessionsCount; $s++) {
                $sessionQuota = min($remainingForDay, $pcCapacity);
                $sessionDistribution["session_$s"] = $sessionQuota;
                $remainingForDay -= $sessionQuota;
            }

            $examDaysList[] = [
                'day_number' => $daysAdded + 1,
                'date' => $dateString,
                'day_total' => $dayTotal,
                'sessions' => $sessionDistribution,
            ];

            $daysAdded++;
            if ($daysAdded < $totalExamDays) {
                $currentDate->addDay();
            }
        }

        $endDateStr = $currentDate->format('Y-m-d');

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
     */
    public function saveEventWithSchedules(array $eventData, array $itemsData): Event
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($eventData, $itemsData) {
            // 1. Determine global min start_date and max end_date across all items
            $minStartDate = null;
            $maxEndDate = null;

            foreach ($itemsData as $item) {
                $itemStartDate = $item['start_date'] ?? null;
                $itemEndDate = $item['end_date'] ?? $itemStartDate;

                if ($itemStartDate) {
                    if (!$minStartDate || $itemStartDate < $minStartDate) {
                        $minStartDate = $itemStartDate;
                    }
                }
                if ($itemEndDate) {
                    if (!$maxEndDate || $itemEndDate > $maxEndDate) {
                        $maxEndDate = $itemEndDate;
                    }
                }
            }

            // Create or Update Event
            $event = Event::create([
                'name' => $eventData['name'],
                'formation_year' => $eventData['formation_year'] ?? date('Y'),
                'description' => $eventData['description'] ?? null,
                'procurement_type_id' => $eventData['procurement_type_id'] ?? null,
                'status' => $eventData['status'] ?? 'draft',
                'start_date' => $minStartDate,
                'end_date' => $maxEndDate,
            ]);

            // 2. Group items by location_id
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

                $locationsGrouped[$locationId]['institutions'][] = [
                    'institution_id' => $item['institution_id'],
                    'koordinator_id' => $item['koordinator_id'] ?? null,
                    'it_id' => $item['it_id'] ?? null,
                    'pengawas_id' => $item['pengawas_id'] ?? null,
                    'participants_count' => (int) ($item['participants_count'] ?? 0),
                ];
            }

            // Create EventLocations & EventLocationInstitutions
            foreach ($locationsGrouped as $locData) {
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

                foreach ($locData['institutions'] as $instData) {
                    EventLocationInstitution::create([
                        'event_location_id' => $eventLocation->id,
                        'institution_id' => $instData['institution_id'],
                        'participants_count' => $instData['participants_count'],
                    ]);

                    // Also maintain relation in event_institutions if needed
                    \App\Models\EventInstitution::firstOrCreate([
                        'event_id' => $event->id,
                        'institution_id' => $instData['institution_id'],
                    ]);

                    $roleEmpMap = [
                        'Koordinator' => $instData['koordinator_id'] ?? null,
                        'IT' => $instData['it_id'] ?? null,
                        'Pengawas' => $instData['pengawas_id'] ?? null,
                    ];

                    foreach ($roleEmpMap as $roleName => $empId) {
                        if ($empId) {
                            $existing = \App\Models\EventEmployee::where('event_id', $event->id)
                                ->where('employee_id', $empId)
                                ->first();

                            if ($existing) {
                                $currentRoles = is_array($existing->role) ? $existing->role : [];
                                if (!in_array($roleName, $currentRoles)) {
                                    $currentRoles[] = $roleName;
                                    $existing->update(['role' => $currentRoles]);
                                }
                            } else {
                                \App\Models\EventEmployee::create([
                                    'event_id' => $event->id,
                                    'employee_id' => $empId,
                                    'role' => [$roleName],
                                ]);
                            }
                        }
                    }
                }
            }

            return $event;
        });
    }
}
