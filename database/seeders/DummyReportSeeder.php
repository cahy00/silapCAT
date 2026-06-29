<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\Event;
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
        $user = User::first();

        $events = Event::with('eventLocations')->get();

        if ($events->isEmpty()) {
            $this->command?->warn('Tidak ada kegiatan (event) ditemukan. Buat kegiatan terlebih dahulu.');
            return;
        }

        $reports = [];

        foreach ($events as $event) {
            $locations = $event->eventLocations;
            if ($locations->isEmpty()) {
                continue;
            }

            foreach ($locations as $loc) {
                // Determine dates for simulation
                $startDate = $loc->start_date ?? $event->start_date ?? now()->subDays(3);
                $endDate = $loc->end_date ?? $event->end_date ?? now();

                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                if ($start->gt($end)) {
                    $end = $start->copy()->addDays(3);
                }

                $days = max(1, min(5, $start->diffInDays($end) + 1));

                for ($d = 0; $d < $days; $d++) {
                    $dateStr = $start->copy()->addDays($d)->format('Y-m-d');

                    // Generate 2 to 3 sessions per day
                    $sessionsCount = rand(2, 3);
                    for ($s = 1; $s <= $sessionsCount; $s++) {
                        $total = rand(50, 100);
                        $present = rand(intval($total * 0.85), $total);
                        $absent = $total - $present;

                        $reports[] = [
                            'event_id' => $event->id,
                            'event_location_id' => $loc->id,
                            'user_id' => $user?->id ?? 1,
                            'report_date' => $dateStr,
                            'session_name' => 'Sesi ' . $s,
                            'total_participants' => $total,
                            'present_count' => $present,
                            'absent_count' => $absent,
                            'highest_score' => round($faker->randomFloat(2, 420, 495), 2),
                            'lowest_score' => round($faker->randomFloat(2, 180, 290), 2),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        if (!empty($reports)) {
            Report::insertOrIgnore($reports);
            $this->command?->info(count($reports) . ' data laporan sesi dummy berhasil ditambahkan untuk semua kegiatan.');
        } else {
            $this->command?->warn('Belum ada lokasi kegiatan yang siap disimulasikan laporannya.');
        }
    }
}
