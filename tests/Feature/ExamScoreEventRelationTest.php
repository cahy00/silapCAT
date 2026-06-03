<?php

use App\Models\Event;
use App\Models\ExamScore;
use App\Models\ProcurementCategory;
use App\Models\ProcurementType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an exam score belongs to an event and an event has many exam scores', function () {
    // 1. Create a Procurement Category & Type
    $category = ProcurementCategory::create([
        'name' => 'Tes Pengembangan Karir'
    ]);

    $type = ProcurementType::create([
        'name' => 'UD',
        'procurement_category_id' => $category->id
    ]);

    // 2. Create an Event
    $event = Event::create([
        'name' => 'Ujian Dinas Tingkat I Tahun 2026',
        'formation_year' => 2026,
        'status' => 'active',
        'procurement_type_id' => $type->id
    ]);

    // 3. Create an ExamScore linked to the event
    $examScore = ExamScore::create([
        'event_id' => $event->id,
        'employee_number' => '199507112020031005',
        'name' => 'John Doe',
        'position' => 'Analis Sistem',
        'institution' => 'BKD',
        'exam_type' => 'UD_I',
        'exam_date' => '2026-05-26',
        'cat_score' => 400.00,
        'notes' => 'Lulus tes',
    ]);

    // 4. Assert relationships are correct
    expect($examScore->event)->not->toBeNull();
    expect($examScore->event->id)->toBe($event->id);
    expect($examScore->event->name)->toBe('Ujian Dinas Tingkat I Tahun 2026');

    expect($event->examScores)->toHaveCount(1);
    expect($event->examScores->first()->id)->toBe($examScore->id);
    expect($event->examScores->first()->name)->toBe('John Doe');
});
