<?php

use App\Models\Event;
use App\Models\EventLocation;
use App\Models\Location;
use App\Models\ProcurementCategory;
use App\Models\ProcurementType;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('v1 reports api returns reports ordered by latest date and requires api key', function () {
    $apiKey = config('app.api_key', env('API_KEY', 'silapcat-secret-key-2026'));

    // Unauthorized without API key
    $unauthorized = $this->getJson('/api/v1/reports');
    $unauthorized->assertStatus(401);

    // Setup event and location
    $category = ProcurementCategory::create(['name' => 'Kategori API']);
    $type = ProcurementType::create(['name' => 'Tipe API', 'procurement_category_id' => $category->id]);
    $event = Event::create([
        'name' => 'Event API Test',
        'formation_year' => 2026,
        'status' => 'active',
        'procurement_type_id' => $type->id,
    ]);

    $loc = Location::create(['name' => 'Lokasi Uji API', 'address' => 'Jl. Uji']);
    $eventLocation = EventLocation::create([
        'event_id' => $event->id,
        'location_id' => $loc->id,
    ]);

    // Create 2 reports with different dates
    $oldReport = Report::create([
        'event_id' => $event->id,
        'event_location_id' => $eventLocation->id,
        'report_date' => '2026-01-10',
        'session_name' => 'Sesi 1',
        'present_count' => 10,
        'absent_count' => 2,
        'total_participants' => 12,
    ]);

    $newReport = Report::create([
        'event_id' => $event->id,
        'event_location_id' => $eventLocation->id,
        'report_date' => '2026-09-25',
        'session_name' => 'Sesi 2',
        'present_count' => 20,
        'absent_count' => 5,
        'total_participants' => 25,
    ]);

    // Authorized request with API key
    $response = $this->withHeaders(['X-API-KEY' => $apiKey])->getJson('/api/v1/reports');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            '*' => [
                'id',
                'event_id',
                'event_name',
                'location_name',
                'report_date',
                'session_name',
                'total_participants',
                'present_count',
                'absent_count',
            ],
        ],
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(2);
    // Newest date must be first
    expect($data[0]['id'])->toBe($newReport->id);
    expect($data[0]['report_date'])->toBe('2026-09-25');
    expect($data[1]['id'])->toBe($oldReport->id);
    expect($data[1]['report_date'])->toBe('2026-01-10');

    // Test single report endpoint
    $single = $this->withHeaders(['X-API-KEY' => $apiKey])->getJson("/api/v1/reports/{$newReport->id}");
    $single->assertStatus(200);
    $single->assertJsonPath('data.total_participants', 25);
});
