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

    $response = $this->actingAs($user)->get(route('events.all-excel'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
