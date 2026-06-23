<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\EventLocation;
use Carbon\Carbon;

class EventCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Event';
    protected static ?string $title = 'Kalender Jadwal Kegiatan';
    protected static ?string $slug = 'event-calendar';

    protected string $view = 'filament.pages.event-calendar';

    public function getEvents(): array
    {
        $events = [];

        // Fetch all event locations and relations to build detailed tooltips
        $eventLocations = EventLocation::with(['event', 'location', 'event.eventEmployees.employee'])->get();

        foreach ($eventLocations as $el) {
            if (!$el->start_date || !$el->end_date) continue;

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

            $eventName = $el->event->name ?? 'Tanpa Nama Event';
            $locationName = $el->location->name ?? 'Unknown Location';
            $startDateFmt = Carbon::parse($el->start_date)->translatedFormat('d F Y');
            $endDateFmt = Carbon::parse($el->end_date)->translatedFormat('d F Y');

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
                                <span>{$startDateFmt} <span class='text-gray-400 mx-1'>s/d</span> {$endDateFmt}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class='flex items-center mb-3.5'>
                        <div class='h-px bg-gray-200 dark:bg-gray-700 flex-1'></div>
                        <h4 class='text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest px-3 flex items-center'>
                            <svg style='width: 14px; height: 14px;' class='mr-1.5 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' /></svg>
                            Susunan Petugas
                        </h4>
                        <div class='h-px bg-gray-200 dark:bg-gray-700 flex-1'></div>
                    </div>
                    {$employeesStr}
                </div>
            </div>
            ";

            $events[] = [
                'id' => $el->id,
                'title' => $eventName . ' - ' . $locationName,
                'start' => $el->start_date,
                // Fullcalendar end date is exclusive, so we add 1 day
                'end' => Carbon::parse($el->end_date)->addDay()->format('Y-m-d'),
                'color' => '#4f46e5',
                'extendedProps' => [
                    'details' => $detailsHtml,
                ],
            ];
        }

        return $events;
    }
}
