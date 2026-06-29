<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Event;
use App\Models\EventLocation;
use App\Models\Location;
use Carbon\Carbon;
use Livewire\Attributes\On;

class EventCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Kegiatan';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Kalender Jadwal Kegiatan';
    protected static ?string $slug = 'event-calendar';

    protected string $view = 'filament.pages.event-calendar';

    public function getEvents(): array
    {
        $events = [];

        $eventLocations = EventLocation::with([
            'event.procurementType',
            'location',
            'event.eventEmployees.employee',
            'eventLocationInstitutions.institution',
        ])->get();

        // Color palette based on procurement type
        $colorMap = [
            'CPNS' => ['bg' => '#4f46e5', 'border' => '#4338ca'],
            'PPPK' => ['bg' => '#0891b2', 'border' => '#0e7490'],
            'UD'   => ['bg' => '#d97706', 'border' => '#b45309'],
            'UPKP' => ['bg' => '#059669', 'border' => '#047857'],
            'UD/UPKP' => ['bg' => '#7c3aed', 'border' => '#6d28d9'],
        ];
        $defaultColor = ['bg' => '#6366f1', 'border' => '#4f46e5'];

        // Status color mapping
        $statusColors = [
            'draft' => ['bg' => '#6b7280', 'text' => 'Draft'],
            'active' => ['bg' => '#059669', 'text' => 'Aktif'],
            'completed' => ['bg' => '#2563eb', 'text' => 'Selesai'],
            'cancelled' => ['bg' => '#dc2626', 'text' => 'Dibatalkan'],
        ];

        foreach ($eventLocations as $el) {
            if (!$el->start_date || !$el->end_date) continue;

            $eventName = $el->event->name ?? 'Tanpa Nama Event';
            $locationName = $el->location->name ?? 'Unknown Location';
            $procType = $el->event->procurementType->name ?? '';
            $status = $el->event->status ?? 'draft';
            $color = $colorMap[$procType] ?? $defaultColor;
            $statusInfo = $statusColors[$status] ?? $statusColors['draft'];

            $startDateFmt = Carbon::parse($el->start_date)->translatedFormat('d F Y');
            $endDateFmt = Carbon::parse($el->end_date)->translatedFormat('d F Y');

            // Duration calculation
            $duration = Carbon::parse($el->start_date)->diffInDays(Carbon::parse($el->end_date)) + 1;

            // Institutions
            $institutions = $el->eventLocationInstitutions->map(fn($i) => $i->institution->name ?? '-')->implode(', ');
            $instCount = $el->eventLocationInstitutions->count();
            $totalParticipants = $el->eventLocationInstitutions->sum('participants_count');

            // Employees
            $employeesStr = "";
            if ($el->event && $el->event->eventEmployees && $el->event->eventEmployees->count() > 0) {
                $employeesStr .= "<div class='grid grid-cols-1 sm:grid-cols-2 gap-3'>";
                foreach ($el->event->eventEmployees as $emp) {
                    $rolesArray = is_array($emp->role) ? $emp->role : [];
                    $rolesHtml = '';
                    foreach($rolesArray as $r) {
                        $rolesHtml .= "<span class='inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200 mr-1 mb-1 tracking-wide uppercase shadow-sm border border-indigo-200 dark:border-indigo-800'>{$r}</span>";
                    }
                    if (empty($rolesHtml)) {
                        $rolesHtml = "<span class='text-xs text-gray-500'>Tidak ada peran</span>";
                    }
                    
                    $empName = $emp->employee->name ?? 'Unknown Employee';
                    $nip = $emp->employee->employee_number ?? '-';
                    $initial = strtoupper(substr($empName, 0, 1));
                    
                    $employeesStr .= "
                    <div class='flex items-start space-x-2.5 p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200'>
                        <div class='flex-shrink-0 mt-0.5'>
                            <div class='rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-inner border-2 border-white dark:border-gray-700' style='width: 32px; height: 32px; min-width: 32px;'>
                                {$initial}
                            </div>
                        </div>
                        <div class='flex-1 min-w-0'>
                            <p class='text-xs font-bold text-gray-900 dark:text-white truncate'>{$empName}</p>
                            <p class='text-[10px] text-gray-500 dark:text-gray-400 mb-1 font-medium tracking-wide'>NIP: {$nip}</p>
                            <div class='flex flex-wrap'>{$rolesHtml}</div>
                        </div>
                    </div>";
                }
                $employeesStr .= "</div>";
            } else {
                $employeesStr = "
                <div class='flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-800/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600'>
                    <div class='p-2 bg-white dark:bg-gray-700 rounded-full shadow-sm mb-2'>
                        <svg class='text-gray-400 dark:text-gray-500' style='width: 20px; height: 20px;' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />
                        </svg>
                    </div>
                    <p class='text-xs font-medium text-gray-500 dark:text-gray-400'>Belum ada petugas yang dialokasikan</p>
                </div>";
            }
            
            $dailyScheduleHtml = $this->generateDailyScheduleHtml($el, $totalParticipants);

            $detailsHtml = "
            <div class='space-y-5'>
                <div class='bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/30 dark:to-blue-900/20 rounded-xl p-4 border border-indigo-100/50 dark:border-indigo-800/30 shadow-sm relative overflow-hidden'>
                    <div class='absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-indigo-500/10 dark:bg-indigo-400/10 rounded-full blur-xl'></div>
                    <div class='flex items-start space-x-3.5 relative z-10'>
                        <div class='flex-shrink-0 p-2.5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-indigo-50 dark:border-gray-700 text-indigo-600 dark:text-indigo-400 mt-1' style='width: 44px; height: 44px;'>
                            <svg style='width: 20px; height: 20px;' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                              <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' />
                            </svg>
                        </div>
                        <div class='pt-0.5' style='width: calc(100% - 50px);'>
                            <h4 class='text-base font-bold text-gray-900 dark:text-white leading-tight mb-1.5' style='word-break: break-word;'>{$eventName}</h4>
                            <div class='flex items-center text-xs font-medium text-gray-600 dark:text-gray-300 mt-1.5 space-x-2'>
                                <svg style='width: 16px; height: 16px; min-width: 16px;' class='text-indigo-500 dark:text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 11a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
                                <span>{$locationName}</span>
                            </div>
                            <div class='flex items-center text-xs font-medium text-gray-600 dark:text-gray-300 mt-1 space-x-2'>
                                <svg style='width: 16px; height: 16px; min-width: 16px;' class='text-indigo-500 dark:text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' /></svg>
                                <span>{$startDateFmt} <span class='text-gray-400 mx-1'>s/d</span> {$endDateFmt} <span class='text-indigo-500 font-bold ml-1'>({$duration} hari)</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='grid grid-cols-3 gap-3'>
                    <div class='bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-center'>
                        <div class='text-lg font-bold text-indigo-600 dark:text-indigo-400'>{$instCount}</div>
                        <div class='text-[10px] font-medium text-gray-500 uppercase tracking-wider'>Instansi</div>
                    </div>
                    <div class='bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-center'>
                        <div class='text-lg font-bold text-emerald-600 dark:text-emerald-400'>" . number_format($totalParticipants) . "</div>
                        <div class='text-[10px] font-medium text-gray-500 uppercase tracking-wider'>Peserta</div>
                    </div>
                    <div class='bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-center'>
                        <div class='text-lg font-bold text-amber-600 dark:text-amber-400'>{$duration}</div>
                        <div class='text-[10px] font-medium text-gray-500 uppercase tracking-wider'>Hari</div>
                    </div>
                </div>

                <div>
                    <div class='flex items-center mb-3.5'>
                        <div class='h-px bg-gray-200 dark:bg-gray-700 flex-1'></div>
                        <h4 class='text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest px-3 flex items-center'>
                            <svg style='width: 14px; height: 14px;' class='mr-1.5 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' /></svg>
                            Susunan Petugas
                        </h4>
                    </div>
                    {$employeesStr}
                </div>
                
                {$dailyScheduleHtml}
            </div>
            ";

            $events[] = [
                'id' => $el->id,
                'title' => $eventName . ' — ' . $locationName,
                'start' => $el->start_date->format('Y-m-d'),
                // FullCalendar end date is exclusive, so we add 1 day
                'end' => Carbon::parse($el->end_date)->addDay()->format('Y-m-d'),
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'details' => $detailsHtml,
                    'eventLocationId' => $el->id,
                    'eventId' => $el->event_id,
                    'locationName' => $locationName,
                    'procType' => $procType,
                    'status' => $status,
                    'statusText' => $statusInfo['text'],
                    'duration' => $duration,
                    'participants' => $totalParticipants,
                ],
            ];
        }

        return $events;
    }

    public function getLocations(): array
    {
        return Location::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getEventsList(): array
    {
        return Event::orderByDesc('created_at')->get()->map(function ($e) {
            return [
                'id' => $e->id,
                'name' => $e->name,
                'year' => $e->formation_year,
            ];
        })->toArray();
    }

    public function getStats(): array
    {
        $eventLocations = EventLocation::with('event')->get();
        $now = Carbon::today();

        return [
            'total' => $eventLocations->count(),
            'active' => $eventLocations->filter(fn($el) => $el->start_date && $el->end_date && $el->start_date <= $now && $el->end_date >= $now)->count(),
            'upcoming' => $eventLocations->filter(fn($el) => $el->start_date && $el->start_date > $now)->count(),
            'completed' => $eventLocations->filter(fn($el) => $el->end_date && $el->end_date < $now)->count(),
        ];
    }

    #[On('saveEventLocation')]
    public function saveEventLocation(int $eventId, int $locationId, string $startDate, string $endDate): void
    {
        EventLocation::create([
            'event_id' => $eventId,
            'location_id' => $locationId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->dispatch('calendar-refresh');

        \Filament\Notifications\Notification::make()
            ->title('Jadwal Berhasil Ditambahkan')
            ->body('Jadwal kegiatan telah ditambahkan ke kalender.')
            ->success()
            ->send();
    }

    #[On('updateEventLocationDates')]
    public function updateEventLocationDates(int $eventLocationId, string $startDate, string $endDate): void
    {
        $el = EventLocation::find($eventLocationId);
        if ($el) {
            $el->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        $this->dispatch('calendar-refresh');

        \Filament\Notifications\Notification::make()
            ->title('Jadwal Berhasil Diperbarui')
            ->body('Tanggal kegiatan telah diubah.')
            ->success()
            ->send();
    }

    #[On('deleteEventLocation')]
    public function deleteEventLocation(int $eventLocationId): void
    {
        $el = EventLocation::find($eventLocationId);
        if ($el) {
            $el->delete();
        }

        $this->dispatch('calendar-refresh');

        \Filament\Notifications\Notification::make()
            ->title('Jadwal Dihapus')
            ->body('Jadwal kegiatan telah dihapus dari kalender.')
            ->warning()
            ->send();
    }

    private function generateDailyScheduleHtml(EventLocation $el, int $totalParticipants): string
    {
        $capacity = $el->location->locationSurvey->pc_count ?? 0;
        if ($capacity <= 0 || $totalParticipants <= 0 || !$el->start_date || !$el->end_date) {
            return "<div class='mt-4 p-4 text-sm text-amber-600 bg-amber-50 rounded-lg border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/50'>⚠️ Tidak dapat memuat Rincian Jadwal Harian (Kapasitas PC belum diisi pada survey lokasi).</div>";
        }

        $sessionType = $el->session_type ?? '4_sessions';
        $hasOpeningDay = (bool) $el->has_opening_day;
        $holidayDatesRaw = is_string($el->holiday_dates) ? json_decode($el->holiday_dates, true) : ($el->holiday_dates ?? []);
        
        $holidayDates = collect($holidayDatesRaw)->map(function ($d) {
            $dateString = is_array($d) ? ($d['date'] ?? null) : $d;
            return $dateString ? Carbon::parse($dateString)->format('Y-m-d') : null;
        })->filter()->toArray();

        $totalSessions = ceil($totalParticipants / $capacity);
        $remainingSessions = $totalSessions;
        $currentDate = Carbon::parse($el->start_date);
        $endDateObj = Carbon::parse($el->end_date);
        $daysNeeded = 0;
        $dailySchedule = [];

        $isSkippedDay = function (Carbon $date) use ($holidayDates) {
            if ($date->isSunday()) return true;
            if (in_array($date->format('Y-m-d'), $holidayDates)) return true;
            return false;
        };

        while ($remainingSessions > 0 && $currentDate->lte($endDateObj)) {
            if ($isSkippedDay($currentDate)) {
                $currentDate->addDay();
                continue;
            }
            
            $daysNeeded++;
            $isFriday = $currentDate->isFriday();
            
            if ($sessionType === '4_sessions') {
                $sessionsToday = $isFriday ? 2 : 4;
            } else {
                $sessionsToday = $isFriday ? 2 : 3;
            }
            
            if ($hasOpeningDay && $daysNeeded === 1) {
                $sessionsToday--;
            }
            
            $actualSessions = min($sessionsToday, $remainingSessions);
            $dailySchedule[] = [
                'day' => $daysNeeded,
                'date' => $currentDate->translatedFormat('D, d M Y'),
                'sessions' => $actualSessions,
                'isFriday' => $isFriday,
                'isOpening' => $hasOpeningDay && $daysNeeded === 1,
            ];
            
            $remainingSessions -= $sessionsToday;
            $currentDate->addDay();
        }

        $rows = '';
        $no = 0;
        $remaining = $totalParticipants;

        foreach ($dailySchedule as $day) {
            $no++;
            $notes = [];
            if ($day['isOpening']) $notes[] = '🎤 Pembukaan';
            if ($day['isFriday']) $notes[] = '🕌 Jumat';
            $noteStr = !empty($notes) ? implode(', ', $notes) : '-';
            
            $sessionDetails = [];
            for ($i = 1; $i <= $day['sessions']; $i++) {
                $peserta = min($capacity, $remaining);
                $sessionDetails[] = $peserta;
                $remaining -= $peserta;
                if ($remaining < 0) $remaining = 0;
            }
            $sessionStr = implode(' + ', array_map(fn($p) => "{$p}", $sessionDetails));
            $totalDay = array_sum($sessionDetails);
            
            $bgColor = $no % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900';
            
            $rows .= "<tr class='border-b border-gray-200 dark:border-gray-700 {$bgColor}'>
                <td class='p-2.5 text-xs text-gray-500 dark:text-gray-400 text-center'>{$no}</td>
                <td class='p-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap'>{$day['date']}</td>
                <td class='p-2.5 text-center'>
                    <span class='inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 font-bold text-[10px] ring-1 ring-indigo-500/20'>{$day['sessions']}</span>
                </td>
                <td class='p-2.5 text-xs text-gray-600 dark:text-gray-400 font-mono tracking-tight'>{$sessionStr}</td>
                <td class='p-2.5 text-xs font-bold text-gray-900 dark:text-white text-center'>{$totalDay}</td>
                <td class='p-2.5 text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap'>{$noteStr}</td>
            </tr>";
        }
        
        $totalSes = collect($dailySchedule)->sum('sessions');
        $tableHtml = "
        <div class='mt-5'>
            <div class='flex items-center mb-3.5'>
                <div class='h-px bg-gray-200 dark:bg-gray-700 flex-1'></div>
                <h4 class='text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest px-3 flex items-center'>
                    <svg style='width: 14px; height: 14px;' class='mr-1.5 text-emerald-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' /></svg>
                    Rincian Jadwal Harian
                </h4>
                <div class='h-px bg-gray-200 dark:bg-gray-700 flex-1'></div>
            </div>
            
            <div class='w-full overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm'>
                <table class='w-full text-left border-collapse min-w-[500px]'>
                    <thead>
                        <tr class='bg-gray-100 dark:bg-gray-800 border-b-2 border-gray-200 dark:border-gray-700'>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center w-10'>No</th>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider'>Hari / Tanggal</th>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center w-12'>Sesi</th>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider'>Peserta/Sesi</th>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center'>Total</th>
                            <th class='p-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider'>Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                    <tfoot>
                        <tr class='bg-gray-50 dark:bg-gray-800/80 border-t-2 border-gray-300 dark:border-gray-600'>
                            <td colspan='2' class='p-2.5 text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider text-right pr-4'>TOTAL</td>
                            <td class='p-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 text-center'>{$totalSes}</td>
                            <td></td>
                            <td class='p-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 text-center'>{$totalParticipants}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>";

        return $tableHtml;
    }
}
