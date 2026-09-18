<?php

use App\Models\Employee;
use App\Services\GeneticSchedulerService;
use Illuminate\Database\Eloquent\Collection;

test('calculates correct officer requirements based on pc capacity and room count', function () {
    $service = new GeneticSchedulerService();

    // 100 PC, 2 Ruangan: Koordinator=1, IT=ceil(100/50)=2, Pengawas=max(2, ceil(100/25))=4
    $req1 = $service->calculateRequirements([
        'pc_capacity' => 100,
        'room_count' => 2,
    ]);
    expect($req1['koordinator'])->toBe(1);
    expect($req1['it'])->toBe(2);
    expect($req1['pengawas'])->toBe(4);

    // 50 PC, 3 Ruangan (Ruangan lebih banyak dari PC/25): Koordinator=1, IT=1, Pengawas=max(3, 2)=3
    $req2 = $service->calculateRequirements([
        'pc_capacity' => 50,
        'room_count' => 3,
    ]);
    expect($req2['koordinator'])->toBe(1);
    expect($req2['it'])->toBe(1);
    expect($req2['pengawas'])->toBe(3);

    // 300 PC, 5 Ruangan: Koordinator=1, IT=6 (300/50), Pengawas=12 (300/25)
    $req3 = $service->calculateRequirements([
        'pc_capacity' => 300,
        'room_count' => 5,
    ]);
    expect($req3['koordinator'])->toBe(1);
    expect($req3['it'])->toBe(6);
    expect($req3['pengawas'])->toBe(12);
});

test('genetic algorithm assigns officers without schedule conflicts when capacity is sufficient', function () {
    $service = new GeneticSchedulerService();

    // Mock pool of employees with distinct roles
    $employeesData = [
        // Koordinators
        ['id' => 1, 'name' => 'Koord 1', 'status' => ['Koordinator']],
        ['id' => 2, 'name' => 'Koord 2', 'status' => ['Koordinator']],
        // IT
        ['id' => 3, 'name' => 'IT 1', 'status' => ['IT']],
        ['id' => 4, 'name' => 'IT 2', 'status' => ['IT']],
        ['id' => 5, 'name' => 'IT 3', 'status' => ['IT']],
        ['id' => 6, 'name' => 'IT 4', 'status' => ['IT']],
        // Pengawas
        ['id' => 7, 'name' => 'Pengawas 1', 'status' => ['Pengawas']],
        ['id' => 8, 'name' => 'Pengawas 2', 'status' => ['Pengawas']],
        ['id' => 9, 'name' => 'Pengawas 3', 'status' => ['Pengawas']],
        ['id' => 10, 'name' => 'Pengawas 4', 'status' => ['Pengawas']],
        ['id' => 11, 'name' => 'Pengawas 5', 'status' => ['Pengawas']],
        ['id' => 12, 'name' => 'Pengawas 6', 'status' => ['Pengawas']],
    ];

    $employees = new Collection(array_map(fn ($e) => (object) $e, $employeesData));

    // Two overlapping tilok on the same date: 2026-10-01
    $items = [
        [
            'location_id' => 101,
            'pc_capacity' => 50, // req: K:1, IT:1, P:2
            'room_count' => 1,
            'participants_count' => 300,
            'start_date' => '2026-10-01',
        ],
        [
            'location_id' => 102,
            'pc_capacity' => 50, // req: K:1, IT:1, P:2
            'room_count' => 1,
            'participants_count' => 300,
            'start_date' => '2026-10-01',
        ],
    ];

    $scheduleCalculations = [
        0 => [
            'days' => [
                ['date' => '2026-10-01'],
            ],
        ],
        1 => [
            'days' => [
                ['date' => '2026-10-01'],
            ],
        ],
    ];

    $result = $service->run($items, $employees, $scheduleCalculations);

    expect($result['has_conflicts'])->toBeFalse();
    expect($result['assignments'])->toHaveCount(2);

    $assign1 = $result['assignments'][0];
    $assign2 = $result['assignments'][1];

    expect(count($assign1['koordinator_ids']))->toBe(1);
    expect(count($assign1['it_ids']))->toBe(1);
    expect(count($assign1['pengawas_ids']))->toBe(2);

    expect(count($assign2['koordinator_ids']))->toBe(1);
    expect(count($assign2['it_ids']))->toBe(1);
    expect(count($assign2['pengawas_ids']))->toBe(2);

    // Assert no overlap between assign1 and assign2
    $allOfficers1 = array_merge($assign1['koordinator_ids'], $assign1['it_ids'], $assign1['pengawas_ids']);
    $allOfficers2 = array_merge($assign2['koordinator_ids'], $assign2['it_ids'], $assign2['pengawas_ids']);
    $intersection = array_intersect($allOfficers1, $allOfficers2);

    expect($intersection)->toBeEmpty();
});
