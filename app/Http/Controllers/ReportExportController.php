<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

    public function recapExcel(Request $request)
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Laporan Harian');

        // Title Row 1
        $sheet->setCellValue('A1', 'REKAPITULASI LAPORAN HARIAN PELAKSANAAN UJIAN');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Subtitle Row 2
        $subTitle = ($event ? 'Kegiatan: ' . $event->name : 'Semua Kegiatan (Gabungan)');
        if ($monthName && $request->year) {
            $subTitle .= ' | Periode: ' . $monthName . ' ' . $request->year;
        } elseif ($request->year) {
            $subTitle .= ' | Tahun: ' . $request->year;
        }
        $sheet->setCellValue('A2', $subTitle);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Summary Boxes Row 4 & 5
        $sheet->setCellValue('B4', 'Total Sesi Laporan');
        $sheet->setCellValue('D4', 'Total Peserta Hadir');
        $sheet->setCellValue('F4', 'Total Tidak Hadir');
        $sheet->setCellValue('H4', 'Nilai Tertinggi');
        $sheet->setCellValue('J4', 'Nilai Terendah');
        $sheet->getStyle('B4:J4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $totalReports = $reports->count();
        $presentCount = $reports->sum('present_count');
        $absentCount = $reports->sum('absent_count');
        $highestScore = $reports->max('highest_score') ?? 0;
        $minReports = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
        $lowestScore = $minReports->isNotEmpty() ? $minReports->min('lowest_score') : ($reports->min('lowest_score') ?? 0);

        $sheet->setCellValue('B5', $totalReports . ' Sesi');
        $sheet->setCellValue('D5', $presentCount . ' Peserta');
        $sheet->setCellValue('F5', $absentCount . ' Peserta');
        $sheet->setCellValue('H5', number_format($highestScore, 2, ',', '.'));
        $sheet->setCellValue('J5', number_format($lowestScore, 2, ',', '.'));
        $sheet->getStyle('B5:J5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);

        // Table Headers Row 7
        $headers = [
            'No', 'Tanggal Laporan', 'Nama / Sesi', 'Kegiatan', 'Titik Lokasi',
            'Peserta Hadir', 'Tidak Hadir', 'Total Peserta',
            'Nilai Tertinggi', 'Nilai Terendah', 'Petugas Pelapor'
        ];
        foreach ($headers as $colIdx => $header) {
            $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colString . '7', $header);
        }
        $sheet->getStyle('A7:K7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(25);

        // Data Rows starting Row 8
        $row = 8;
        foreach ($reports as $idx => $rep) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $rep->report_date ? $rep->report_date->format('Y-m-d') : '-');
            $sheet->setCellValue('C' . $row, $rep->session_name ?? '-');
            $sheet->setCellValue('D' . $row, $rep->event->name ?? '-');
            $sheet->setCellValue('E' . $row, $rep->eventLocation->location->name ?? '-');
            $sheet->setCellValue('F' . $row, (int) $rep->present_count);
            $sheet->setCellValue('G' . $row, (int) $rep->absent_count);
            $sheet->setCellValue('H' . $row, (int) $rep->total_participants);
            $sheet->setCellValue('I' . $row, $rep->highest_score !== null ? (float) $rep->highest_score : '');
            $sheet->setCellValue('J' . $row, $rep->lowest_score !== null ? (float) $rep->lowest_score : '');
            $sheet->setCellValue('K' . $row, $rep->user->name ?? '-');

            // Align center for A, B, C, F, G, H, I, J
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        if ($reports->count() > 0) {
            $lastRow = $row - 1;
            $sheet->getStyle('A8:K' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekapitulasi_Laporan_Harian_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
