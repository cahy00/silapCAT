<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Models\Event;
use App\Models\EventInstitution;
use App\Models\EventLocation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalEvents = Event::count();
        $activeEvents = Event::where('status', 'active')->count();
        $totalParticipants = EventInstitution::sum('participants_count');
        $locationsCount = EventLocation::distinct('location_id')->count('location_id');

        return [
            Stat::make('Total Kegiatan', $totalEvents)
                ->description('Seluruh event terdaftar')
                ->descriptionIcon('heroicon-m-calendar')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('primary'),
            Stat::make('Kegiatan Aktif', $activeEvents)
                ->description('Sedang berlangsung')
                ->descriptionIcon('heroicon-m-bolt')
                ->chart([2, 4, 3, 4, 6, 4, 7, 5])
                ->color('success'),
            Stat::make('Total Peserta', number_format($totalParticipants))
                ->description('Akumulasi kuota peserta')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([15, 4, 10, 2, 12, 4, 12, 10])
                ->color('info'),
            Stat::make('Lokasi Digunakan', $locationsCount)
                ->description('Titik lokasi terlibat')
                ->descriptionIcon('heroicon-m-map-pin')
                ->chart([10, 15, 8, 12, 10, 12, 15, 12])
                ->color('warning'),
        ];
    }
}
