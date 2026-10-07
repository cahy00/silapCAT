<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentZipExportController extends Controller
{
    /**
     * Export all documents of a single event into a ZIP archive.
     */
    public function exportSingleEvent(Event $event)
    {
        $zipFileName = 'Dokumen_' . Str::slug($event->name) . '_' . ($event->formation_year ?? now()->year) . '.zip';
        $tempZipPath = storage_path('app/temp_' . uniqid() . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $docsFound = 0;
        $docDefinitions = [
            'doc_implementation_report' => '01_Laporan_Pelaksanaan',
            'doc_team_decree' => '02_SK_Tim_Pelaksana',
            'doc_ba_catos' => '03_Berita_Acara_CATOS',
            'doc_institution_announcement' => '04_Pengumuman_Instansi',
        ];

        foreach ($docDefinitions as $field => $prefix) {
            $filePath = $event->{$field};
            if (!empty($filePath)) {
                $fullPath = $this->resolveFilePath($filePath);
                if ($fullPath && file_exists($fullPath)) {
                    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $zipEntryName = $prefix . '_' . Str::slug($event->name) . '.' . $ext;
                    $zip->addFile($fullPath, $zipEntryName);
                    $docsFound++;
                }
            }
        }

        // Include generated Event Report PDF
        try {
            $pdf = Pdf::loadView('pdf.event-report', ['event' => $event]);
            $pdfContent = $pdf->output();
            $zip->addFromString('00_Ringkasan_Kegiatan_' . Str::slug($event->name) . '.pdf', $pdfContent);
            $docsFound++;
        } catch (\Throwable $e) {
            // ignore pdf generation error if any
        }

        $zip->close();

        if ($docsFound === 0) {
            @unlink($tempZipPath);
            return back()->with('notification', [
                'type' => 'warning',
                'title' => 'Dokumen Kosong',
                'body' => 'Belum ada dokumen yang diunggah untuk kegiatan ini.',
            ]);
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Export documents for multiple events (by filter or selected IDs) into a structured ZIP archive.
     */
    public function exportBulk(Request $request)
    {
        $query = Event::with(['procurementType', 'eventLocations.location', 'reports']);

        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('event_id')) {
                $query->where('id', $request->event_id);
            }
            if ($request->filled('year')) {
                $query->where(function ($q) use ($request) {
                    $q->where('formation_year', $request->year)
                      ->orWhereYear('start_date', $request->year);
                });
            }
            if ($request->filled('month')) {
                $query->whereMonth('start_date', $request->month);
            }
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        $events = $query->orderBy('start_date', 'desc')->get();

        if ($events->isEmpty()) {
            return back()->with('notification', [
                'type' => 'warning',
                'title' => 'Data Kosong',
                'body' => 'Tidak ditemukan kegiatan yang sesuai dengan filter yang dipilih.',
            ]);
        }

        $zipFileName = 'Dokumen_Kegiatan_SILAPCAT_' . now()->format('Ymd_His') . '.zip';
        $tempZipPath = storage_path('app/temp_bulk_' . uniqid() . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $totalFiles = 0;
        $docDefinitions = [
            'doc_implementation_report' => '01_Laporan_Pelaksanaan',
            'doc_team_decree' => '02_SK_Tim_Pelaksana',
            'doc_ba_catos' => '03_Berita_Acara_CATOS',
            'doc_institution_announcement' => '04_Pengumuman_Instansi',
        ];

        foreach ($events as $index => $event) {
            $folderName = sprintf('%02d_%s', $index + 1, Str::slug($event->name));
            $zip->addEmptyDir($folderName);

            // Add uploaded files
            foreach ($docDefinitions as $field => $prefix) {
                $filePath = $event->{$field};
                if (!empty($filePath)) {
                    $fullPath = $this->resolveFilePath($filePath);
                    if ($fullPath && file_exists($fullPath)) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $zipEntryName = $folderName . '/' . $prefix . '.' . $ext;
                        $zip->addFile($fullPath, $zipEntryName);
                        $totalFiles++;
                    }
                }
            }

            // Include generated event PDF report in folder
            try {
                $pdf = Pdf::loadView('pdf.event-report', ['event' => $event]);
                $pdfContent = $pdf->output();
                $zip->addFromString($folderName . '/00_Ringkasan_Laporan.pdf', $pdfContent);
                $totalFiles++;
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $zip->close();

        if ($totalFiles === 0) {
            @unlink($tempZipPath);
            return back()->with('notification', [
                'type' => 'warning',
                'title' => 'Dokumen Kosong',
                'body' => 'Tidak ada file dokumen yang ditemukan untuk kegiatan terpilih.',
            ]);
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Resolve absolute file path from public disk or storage.
     */
    private function resolveFilePath(string $path): ?string
    {
        // Check in storage/app/public/
        $storagePath = storage_path('app/public/' . $path);
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        // Check in public/storage/
        $publicStoragePath = public_path('storage/' . $path);
        if (file_exists($publicStoragePath)) {
            return $publicStoragePath;
        }

        // Check in public/
        $directPublic = public_path($path);
        if (file_exists($directPublic)) {
            return $directPublic;
        }

        // Storage disk public path
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        return null;
    }
}
