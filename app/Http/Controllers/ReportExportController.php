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
}
