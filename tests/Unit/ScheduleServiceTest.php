<?php

use App\Services\ScheduleService;

test('calculates correct daily and session breakdown for location item', function () {
    $service = new ScheduleService();

    // 2026-09-14 is Monday
    $item = [
        'pc_capacity' => 100,
        'sessions_per_day' => 4,
        'participants_count' => 950,
        'start_date' => '2026-09-14',
        'has_opening_day' => false,
        'holiday_dates' => [],
    ];

    $result = $service->calculateLocationSchedule($item);

    expect($result['daily_capacity'])->toBe(400);
    expect($result['total_exam_days'])->toBe(3); // 400 (Mon) + 400 (Tue) + 150 (Wed) = 950
    expect($result['start_date'])->toBe('2026-09-14');
    expect($result['end_date'])->toBe('2026-09-16');

    // Day 1
    expect($result['days'][0]['day_total'])->toBe(400);
    expect($result['days'][0]['sessions']['session_1'])->toBe(100);
    expect($result['days'][0]['sessions']['session_4'])->toBe(100);

    // Day 3 (Last Day: 150 remaining)
    expect($result['days'][2]['day_total'])->toBe(150);
    expect($result['days'][2]['sessions']['session_1'])->toBe(100);
    expect($result['days'][2]['sessions']['session_2'])->toBe(50);
    expect($result['days'][2]['sessions']['session_3'])->toBe(0);
});

test('restricts Friday to 2 sessions and skips Sunday execution', function () {
    $service = new ScheduleService();

    // 2026-10-02 is Friday, 2026-10-03 is Saturday, 2026-10-04 is Sunday
    $item = [
        'pc_capacity' => 100,
        'sessions_per_day' => 4,
        'participants_count' => 800, // Fri: 200 (max 2 sessions), Sat: 400, Sun: skipped, Mon (2026-10-05): 200
        'start_date' => '2026-10-02',
        'has_opening_day' => false,
        'holiday_dates' => [],
    ];

    $result = $service->calculateLocationSchedule($item);

    expect($result['total_exam_days'])->toBe(3);
    expect($result['days'][0]['date'])->toBe('2026-10-02'); // Friday
    expect($result['days'][0]['day_total'])->toBe(200); // 2 sessions max on Friday
    expect(count($result['days'][0]['sessions']))->toBe(2);

    expect($result['days'][1]['date'])->toBe('2026-10-03'); // Saturday
    expect($result['days'][1]['day_total'])->toBe(400);

    // Sunday (2026-10-04) is skipped, so day 3 is Monday (2026-10-05)
    expect($result['days'][2]['date'])->toBe('2026-10-05'); // Monday
    expect($result['days'][2]['day_total'])->toBe(200);
});

test('calculates correct grouped schedule for multiple institutions sharing same location capacity', function () {
    $service = new ScheduleService();

    $items = [
        [
            'index' => 0,
            'institution_id' => 1,
            'location_id' => 10,
            'pc_capacity' => 75,
            'sessions_per_day' => 2,
            'participants_count' => 300, // Needs 2 full days (150 per day)
            'start_date' => '2026-09-14',
        ],
        [
            'index' => 1,
            'institution_id' => 2,
            'location_id' => 10,
            'pc_capacity' => 75,
            'sessions_per_day' => 2,
            'participants_count' => 123, // Should start after instansi 1 finishes / remaining capacity
            'start_date' => '2026-09-14',
        ],
    ];

    $results = $service->calculateGroupedLocationSchedule($items);

    // Instansi 1 gets Day 1 (150) & Day 2 (150)
    expect($results[0]['calculation']['days'][0]['date'])->toBe('2026-09-14');
    expect($results[0]['calculation']['days'][0]['day_total'])->toBe(150);
    expect($results[0]['calculation']['days'][1]['date'])->toBe('2026-09-15');
    expect($results[0]['calculation']['days'][1]['day_total'])->toBe(150);

    // Instansi 2 shifts to Day 3 (2026-09-16) session 1 (75) & session 2 (48) because Day 1 & 2 are full
    expect($results[1]['calculation']['days'][0]['date'])->toBe('2026-09-16');
    expect($results[1]['calculation']['days'][0]['day_total'])->toBe(123);
    expect($results[1]['calculation']['days'][0]['sessions']['session_1'])->toBe(75);
    expect($results[1]['calculation']['days'][0]['sessions']['session_2'])->toBe(48);
});
