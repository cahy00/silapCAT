<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EventExportController extends Controller
{
    public function monthlyPdf(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // Validate month and year bounds
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2020 || $year > 2035) {
            $year = now()->year;
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        // Query events active or scheduled in that month/year
        $events = Event::with([
            'procurementType.procurementCategory',
            'eventInstitutions.institution',
            'eventLocations.location',
            'eventLocations.eventLocationInstitutions.institution',
            'eventEmployees.employee',
            'reports'
        ])
        ->where(function ($q) use ($month, $year) {
            $q->where(function ($q1) use ($month, $year) {
                $q1->whereMonth('start_date', $month)->whereYear('start_date', $year);
            })
            ->orWhere(function ($q2) use ($month, $year) {
                $q2->whereMonth('end_date', $month)->whereYear('end_date', $year);
            })
            ->orWhere(function ($q3) use ($month, $year) {
                // Event spanning across the month
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                $q3->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
            })
            ->orWhereHas('eventLocations', function ($q4) use ($month, $year) {
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                $q4->where(function ($lq) use ($month, $year, $startDate, $endDate) {
                    $lq->whereMonth('start_date', $month)->whereYear('start_date', $year)
                       ->orWhereMonth('end_date', $month)->whereYear('end_date', $year)
                       ->orWhere(function ($spanQ) use ($startDate, $endDate) {
                           $spanQ->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
                       });
                });
            })
            ->orWhere(function ($qFallback) use ($month, $year) {
                // Fallback for events with no dates yet created in that month
                $qFallback->whereNull('start_date')
                          ->whereDoesntHave('eventLocations', function ($lq) {
                              $lq->whereNotNull('start_date');
                          })
                          ->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year);
            });
        })
        ->orderBy('start_date', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();

        $pdf = Pdf::loadView('pdf.events-monthly', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = sprintf('Laporan_Kegiatan_Bulan_%s_%d.pdf', $monthName, $year);

        return $pdf->stream($filename);
    }
}
