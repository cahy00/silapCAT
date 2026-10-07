<?php

namespace App\Console\Commands;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Console\Command;

class SyncEventStatus extends Command
{
    protected $signature = 'events:sync-status';

    protected $description = 'Sinkronkan status event di database berdasarkan tanggal pelaksanaan';

    public function handle(): int
    {
        $updated = 0;

        Event::query()->select(['id', 'status', 'start_date', 'end_date'])->chunkById(200, function ($events) use (&$updated) {
            foreach ($events as $event) {
                $raw = $event->getRawOriginal('status');
                $resolved = EventStatus::resolve($raw, $event->start_date, $event->end_date);

                if ($raw !== $resolved) {
                    Event::whereKey($event->id)->update(['status' => $resolved]);
                    $updated++;
                }
            }
        });

        $this->info("{$updated} event diperbarui.");

        return self::SUCCESS;
    }
}
