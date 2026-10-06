<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can download all events excel matrix', function () {
    $user = User::factory()->create();

    $event = Event::create([
        'name' => 'Event Simulasi Matrix Excel',
        'formation_year' => 2026,
        'status' => 'draft',
    ]);

    $institution = \App\Models\Institution::create([
        'name' => 'Kementerian Keuangan RI',
        'code' => 'KEMENKEU',
    ]);

    $location = \App\Models\Location::create([
        'name' => 'BKN Pusat',
        'address' => 'Jl. Mayjen Sutoyo No. 12',
    ]);

    $eventLocation = $event->eventLocations()->create([
        'location_id' => $location->id,
        'start_date' => '2026-05-01',
        'end_date' => '2026-05-03',
    ]);

    $eventLocation->eventLocationInstitutions()->create([
        'institution_id' => $institution->id,
        'participants_count' => 150,
    ]);

    $response = $this->actingAs($user)->get(route('events.all-excel'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('authenticated user can download excel matrix filtered by month and year', function () {
    $user = User::factory()->create();

    $eventMay = Event::create([
        'name' => 'Kegiatan Bulan Mei',
        'formation_year' => 2026,
        'status' => 'active',
        'start_date' => '2026-05-10',
        'end_date' => '2026-05-15',
    ]);

    $eventJune = Event::create([
        'name' => 'Kegiatan Bulan Juni',
        'formation_year' => 2026,
        'status' => 'active',
        'start_date' => '2026-06-10',
        'end_date' => '2026-06-15',
    ]);

    $response = $this->actingAs($user)->get(route('events.all-excel', [
        'month' => 5,
        'year' => 2026,
        'status' => 'all',
    ]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

