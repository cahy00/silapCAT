<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\Event;
use App\Models\EventLocation;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // We need an event, an event location, and a user to attach these reports to.
        $event = Event::first();
        if (!$event) {
            $event = Event::create([
                'name' => 'Event Simulasi CAT Dummy',
                'description' => 'Event Dummy untuk Testing',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active',
            ]);
        }

        $eventLocation = EventLocation::first();
        if (!$eventLocation && $event) {
            // Need a location first
            $location = \App\Models\Location::firstOrCreate(
                ['name' => 'Lokasi Ujian Dummy'],
                ['address' => 'Jl. Dummy No. 1', 'capacity' => 100]
            );
            $eventLocation = EventLocation::create([
                'event_id' => $event->id,
                'location_id' => $location->id,
                'participants_count' => 100,
                'session_type' => 'sesi',
                'has_opening_day' => false,
            ]);
        }

        $user = User::first();

        $reports = [];
        for ($i = 0; $i < 100; $i++) {
            $total = $faker->numberBetween(50, 100);
            $present = $faker->numberBetween(40, $total);
            $absent = $total - $present;

            $reports[] = [
                'event_id' => $event ? $event->id : null,
                'event_location_id' => $eventLocation ? $eventLocation->id : null,
                'user_id' => $user ? $user->id : null,
                'report_date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                'session_name' => 'Sesi ' . $faker->numberBetween(1, 4),
                'total_participants' => $total,
                'present_count' => $present,
                'absent_count' => $absent,
                'highest_score' => $faker->randomFloat(2, 300, 500),
                'lowest_score' => $faker->randomFloat(2, 100, 299),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid any unique constraint collision loops directly,
        // Actually, we need to be careful with the unique constraint: ['event_id', 'event_location_id', 'report_date', 'session_name']
        // We can just use insertOrIgnore
        Report::insertOrIgnore($reports);
    }
}
