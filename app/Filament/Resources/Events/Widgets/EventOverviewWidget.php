<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Models\Event;
use App\Models\EventLocation;
use Filament\Widgets\Widget;

class EventOverviewWidget extends Widget
{
    protected string $view = 'filament.resources.events.widgets.event-overview';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStatsData(),
        ];
    }

    public function getStatsData(): array
    {
        $totalEvents = Event::count();
        $completedEvents = Event::where('status', \App\Enums\EventStatus::Completed->value)->count();
        $activeEvents = Event::where('status', \App\Enums\EventStatus::Active->value)->count();
        $metrics = \App\Support\ParticipantMetrics::aggregate();
        $totalParticipants = $metrics['target_quota'] ?: $metrics['effective_total'];
        $locationsCount = EventLocation::distinct('location_id')->count('location_id');

        return [
            'total_events' => $totalEvents,
            'completed_events' => $completedEvents,
            'active_events' => $activeEvents,
            'total_participants' => $totalParticipants,
            'locations_count' => $locationsCount,
        ];
    }
}
