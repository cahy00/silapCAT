<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Event;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Institution;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $activeEventsCount = Event::where('status', 'active')->count();
        $totalEventsCount = Event::count();
        
        return [
            Stat::make('Total Kegiatan', $totalEventsCount)
                ->description('Seluruh event yang terdaftar')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Kegiatan Aktif', $activeEventsCount)
                ->description('Event yang sedang berjalan')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($activeEventsCount > 0 ? 'success' : 'gray'),
            Stat::make('Total Pegawai', Employee::count())
                ->description('SDM yang terdata di sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Titik Lokasi', Location::count())
                ->description('Lokasi ujian yang tersedia')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
        ];
    }
}
