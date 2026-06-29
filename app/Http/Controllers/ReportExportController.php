<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController extends Controller
{
    public function download(Report $report)
    {
        // Load the related models
        $report->load(['event', 'eventLocation.location', 'user']);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.report-daily', ['report' => $report]);

        // Return PDF as a download
        $filename = 'Laporan-Harian-' . ($report->event->name ?? 'Kegiatan') . '-' . $report->report_date->format('Ymd') . '-' . str_replace(' ', '', $report->session_name) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function recapPdf(Request $request)
    {
        $query = Report::with(['event', 'eventLocation.location', 'user']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('report_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('report_date', $request->year);
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $reports = $query->orderBy('report_date', 'desc')->orderBy('session_name', 'asc')->get();

        $event = null;
        if ($request->filled('event_id')) {
            $event = \App\Models\Event::find($request->event_id);
        }

        $monthName = null;
        if ($request->filled('month')) {
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $monthName = $months[(int)$request->month] ?? null;
        }

        $pdf = Pdf::loadView('pdf.reports-recap', [
            'reports' => $reports,
            'event' => $event,
            'monthName' => $monthName,
            'year' => $request->year,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'Rekapitulasi_Laporan_Harian_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }
}
