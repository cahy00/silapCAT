<?php

namespace App\Http\Controllers;

use App\Models\ExamScore;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display the public certificate search page.
     */
    public function index()
    {
        return view('certificate.search');
    }

    /**
     * Search for exam scores by employee number (NIP).
     */
    public function search(Request $request)
    {
        $request->validate([
            'employee_number' => 'required|string|min:4|max:50',
        ], [
            'employee_number.required' => 'NIP / Nomor Identitas wajib diisi.',
            'employee_number.min' => 'NIP / Nomor Identitas minimal 4 karakter.',
        ]);

        $employeeNumber = trim($request->input('employee_number'));

        $results = ExamScore::with('event')
            ->where('employee_number', $employeeNumber)
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('certificate.search', [
            'results' => $results,
            'searched' => true,
            'employeeNumber' => $employeeNumber,
        ]);
    }

    /**
     * Download the certificate PDF.
     * Uses Laravel Signed URL for security.
     */
    public function download(Request $request, ExamScore $examScore)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Akses ditolak. Tautan unduhan tidak valid atau telah kedaluwarsa.');
        }

        if ($examScore->status !== 'Lulus') {
            abort(403, 'Sertifikat hanya dapat diunduh untuk peserta yang dinyatakan Lulus.');
        }

        // Load the relationship
        $examScore->load('event');

        $pdf = Pdf::loadView('pdf.certificate', [
            'examScore' => $examScore,
            'downloadDate' => now()->translatedFormat('d F Y'),
        ]);

        // Set paper orientation to Landscape A4 for classic premium certificate look
        $pdf->setPaper('a4', 'landscape');

        // Stream the PDF
        $filename = "Sertifikat_" . str_replace(' ', '_', $examScore->name) . "_" . $examScore->exam_type . ".pdf";
        return $pdf->stream($filename);
    }
}
