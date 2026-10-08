<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\Event;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $year = $this->filters['formation_year'] ?? null;
        $eventId = $this->filters['event_id'] ?? null;

        $cacheKey = 'stats_overview_' . md5(json_encode([$year, $eventId]));

        $data = Cache::remember($cacheKey, 60, function () use ($year, $eventId) {
            $evQuery = Event::query();
            if ($eventId) {
                $evQuery->where('id', $eventId);
            } elseif ($year) {
                $evQuery->where('formation_year', $year);
            }

            $totalEventsCount = (clone $evQuery)->count();
            $activeEventsCount = (clone $evQuery)->where('status', \App\Enums\EventStatus::Active->value)->count();

            $totalEmployees = Employee::count();
            $totalLocations = Location::count();

            return compact('totalEventsCount', 'activeEventsCount', 'totalEmployees', 'totalLocations');
        });

        return [
            Stat::make('Total Kegiatan', $data['totalEventsCount'])
                ->description('Event yang terdaftar')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Kegiatan Aktif', $data['activeEventsCount'])
                ->description('Event yang sedang berjalan')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($data['activeEventsCount'] > 0 ? 'success' : 'gray'),
            Stat::make('Total Pegawai', $data['totalEmployees'])
                ->description('SDM yang terdata di sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Titik Lokasi', $data['totalLocations'])
                ->description('Lokasi ujian yang tersedia')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
        ];
    }
}
