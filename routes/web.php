<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CertificateController;

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Website\AnnouncementController;
use App\Http\Controllers\Website\LandingController;
use App\Http\Controllers\Website\NewsController;

// Public Website Routes
Route::redirect('/', '/admin/login')->name('home');
Route::get('/berita/{slug}', [LandingController::class, 'show'])->name('detail-post');
Route::get('/semua-berita', [NewsController::class, 'allNews'])->name('all-news');
Route::get('/semua-artikel', [NewsController::class, 'allArticle'])->name('all-artikel');
Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcement');
Route::get('/pengumuman/{id}', [AnnouncementController::class, 'show'])->name('detail-announcement');

// Konsultasi Routes
Route::get('/konsultasi', [QuestionController::class, 'index'])->name('konsultasi');
Route::post('/konsultasi', [QuestionController::class, 'store'])->name('konsultasi.store');
Route::get('/konsultasi/all', [QuestionController::class, 'all'])->name('konsultasi.all');
Route::get('/konsultasi/kategori/{id}', [QuestionController::class, 'allCategory'])->name('konsultasi.category');
Route::get('/konsultasi/kota/{id}', [QuestionController::class, 'allCity'])->name('konsultasi.city');

Route::get('/events/monthly-pdf', [\App\Http\Controllers\EventExportController::class, 'monthlyPdf'])
    ->name('events.monthly-pdf')
    ->middleware(['auth']);

Route::get('/events/{event}/pdf', function (\App\Models\Event $event) {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.event-report', ['event' => $event]);
    return $pdf->stream("Laporan_Kegiatan_{$event->id}.pdf");
})->name('events.pdf')->middleware(['auth']);

Route::get('/reports/recap-pdf', [\App\Http\Controllers\ReportExportController::class, 'recapPdf'])
    ->name('reports.recap-pdf')
    ->middleware(['auth']);

Route::get('/reports/recap-excel', [\App\Http\Controllers\ReportExportController::class, 'recapExcel'])
    ->name('reports.recap-excel')
    ->middleware(['auth']);

Route::get('/reports/{report}/pdf', [\App\Http\Controllers\ReportExportController::class, 'download'])
    ->name('reports.pdf')
    ->middleware(['auth']);

// Public Certificate Routes
Route::get('/sertifikat', [CertificateController::class, 'index'])->name('certificate.index');
Route::post('/sertifikat', [CertificateController::class, 'search'])->name('certificate.search');
Route::get('/sertifikat/download/{examScore}', [CertificateController::class, 'download'])->name('certificate.download');

// Excel Import Template Downloads
Route::get('/template/exam-score-import', [\App\Http\Controllers\TemplateExportController::class, 'downloadExamScoreTemplate'])->name('template.exam-score-import')->middleware(['auth']);
Route::get('/template/employee-import', [\App\Http\Controllers\TemplateExportController::class, 'downloadEmployeeTemplate'])->name('template.employee-import')->middleware(['auth']);
Route::get('/template/location-import', [\App\Http\Controllers\TemplateExportController::class, 'downloadLocationTemplate'])->name('template.location-import')->middleware(['auth']);
Route::get('/template/institution-import', [\App\Http\Controllers\TemplateExportController::class, 'downloadInstitutionTemplate'])->name('template.institution-import')->middleware(['auth']);

// Public Short Link Creator
Route::get('/buat-link', [\App\Http\Controllers\PublicShortLinkController::class, 'index'])->name('public.shortlink.create');
Route::post('/buat-link', [\App\Http\Controllers\PublicShortLinkController::class, 'store'])->name('public.shortlink.store')->middleware('throttle:5,1');

// Short Link Redirect
Route::get('/{shortCode}', [\App\Http\Controllers\ShortLinkController::class, 'redirect'])
    ->name('shortlink.redirect')
    ->where('shortCode', '[A-Za-z0-9\-\_]+');

// Fallback Route for Storage Files (solves 404 on shared hosting without symlink)
Route::get('/storage/{path}', function ($path) {
    if (str_contains($path, '..')) {
        abort(403);
    }

    $filePath = storage_path('app/public/' . $path);

    if (! file_exists($filePath) || ! is_file($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');
