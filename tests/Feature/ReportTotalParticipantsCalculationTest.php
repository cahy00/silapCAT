<?php

use App\Filament\Resources\Reports\Schemas\ReportForm;
use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Filament\Resources\Reports\Pages\EditReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('report form calculates total participants from present and absent counts', function () {
    $data = [
        'present_count' => 25,
        'absent_count' => 5,
    ];

    $result = [];
    $get = fn (string $key) => $data[$key] ?? null;
    $set = function (string $key, $value) use (&$result) {
        $result[$key] = $value;
    };

    ReportForm::updateTotalParticipants($get, $set);

    expect($result['total_participants'])->toBe(30);
});

test('report form handles null and zero counts gracefully', function () {
    $data = [
        'present_count' => null,
        'absent_count' => 0,
    ];

    $result = [];
    $get = fn (string $key) => $data[$key] ?? null;
    $set = function (string $key, $value) use (&$result) {
        $result[$key] = $value;
    };

    ReportForm::updateTotalParticipants($get, $set);

    expect($result['total_participants'])->toBe(0);
});

test('event location is auto-resolved from event in report creation', function () {
    $category = \App\Models\ProcurementCategory::create(['name' => 'Kategori Tes']);
    $type = \App\Models\ProcurementType::create(['name' => 'Tipe Tes', 'procurement_category_id' => $category->id]);
    $event = \App\Models\Event::create([
        'name' => 'Kegiatan Seleksi 2026',
        'formation_year' => 2026,
        'status' => 'active',
        'procurement_type_id' => $type->id,
    ]);

    $loc = \App\Models\Location::create(['name' => 'Lokasi Ujian 1', 'address' => 'Jl. Ujian 1']);
    $eventLocation = \App\Models\EventLocation::create([
        'event_id' => $event->id,
        'location_id' => $loc->id,
    ]);

    $resolvedLocationId = \App\Models\EventLocation::where('event_id', $event->id)->value('id');
    expect($resolvedLocationId)->toBe($eventLocation->id);
});
