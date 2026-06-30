<?php

namespace App\Http\Controllers;

use App\Models\ExamScore;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

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

        $template = $examScore->event?->certificate_template ?: 'sertifikat_default.pptx';

        $templatePath = null;
        if (file_exists(storage_path('app/public/' . $template))) {
            $templatePath = storage_path('app/public/' . $template);
        } elseif (file_exists(storage_path('app/public/certificate-templates/' . $template))) {
            $templatePath = storage_path('app/public/certificate-templates/' . $template);
        } elseif (file_exists(public_path($template))) {
            $templatePath = public_path($template);
        } elseif (file_exists(public_path('sertifikat_default.pptx'))) {
            $templatePath = public_path('sertifikat_default.pptx');
        }

        if ($templatePath && str_ends_with(strtolower($templatePath), '.pptx')) {
            $tempFile = tempnam(sys_get_temp_dir(), 'cert_') . '.pptx';
            copy($templatePath, $tempFile);

            $zip = new ZipArchive();
            if ($zip->open($tempFile) === true) {
                $replacements = [
                    '&lt;&lt;NAMA&gt;&gt;' => htmlspecialchars($examScore->name ?? '', ENT_XML1, 'UTF-8'),
                    '&lt;&lt;name&gt;&gt;' => htmlspecialchars($examScore->name ?? '', ENT_XML1, 'UTF-8'),
                    '<<NAMA>>' => htmlspecialchars($examScore->name ?? '', ENT_XML1, 'UTF-8'),
                    '<<name>>' => htmlspecialchars($examScore->name ?? '', ENT_XML1, 'UTF-8'),

                    '&lt;&lt;NIP&gt;&gt;' => htmlspecialchars($examScore->employee_number ?? '', ENT_XML1, 'UTF-8'),
                    '&lt;&lt;nip&gt;&gt;' => htmlspecialchars($examScore->employee_number ?? '', ENT_XML1, 'UTF-8'),
                    '<<NIP>>' => htmlspecialchars($examScore->employee_number ?? '', ENT_XML1, 'UTF-8'),
                    '<<nip>>' => htmlspecialchars($examScore->employee_number ?? '', ENT_XML1, 'UTF-8'),

                    '&lt;&lt;exam_type&gt;&gt;' => htmlspecialchars($examScore->exam_type ?? '', ENT_XML1, 'UTF-8'),
                    '&lt;&lt;EXAM_TYPE&gt;&gt;' => htmlspecialchars($examScore->exam_type ?? '', ENT_XML1, 'UTF-8'),
                    '<<exam_type>>' => htmlspecialchars($examScore->exam_type ?? '', ENT_XML1, 'UTF-8'),
                    '<<EXAM_TYPE>>' => htmlspecialchars($examScore->exam_type ?? '', ENT_XML1, 'UTF-8'),

                    '&lt;&lt;score&gt;&gt;' => number_format($examScore->total_score ?? 0, 2, ',', '.'),
                    '&lt;&lt;SCORE&gt;&gt;' => number_format($examScore->total_score ?? 0, 2, ',', '.'),
                    '<<score>>' => number_format($examScore->total_score ?? 0, 2, ',', '.'),
                    '<<SCORE>>' => number_format($examScore->total_score ?? 0, 2, ',', '.'),

                    '&lt;&lt;status&gt;&gt;' => htmlspecialchars($examScore->status ?? '', ENT_XML1, 'UTF-8'),
                    '&lt;&lt;STATUS&gt;&gt;' => htmlspecialchars($examScore->status ?? '', ENT_XML1, 'UTF-8'),
                    '<<status>>' => htmlspecialchars($examScore->status ?? '', ENT_XML1, 'UTF-8'),
                    '<<STATUS>>' => htmlspecialchars($examScore->status ?? '', ENT_XML1, 'UTF-8'),

                    '&lt;&lt;date&gt;&gt;' => $examScore->exam_date ? $examScore->exam_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    '&lt;&lt;DATE&gt;&gt;' => $examScore->exam_date ? $examScore->exam_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    '<<date>>' => $examScore->exam_date ? $examScore->exam_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    '<<DATE>>' => $examScore->exam_date ? $examScore->exam_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                ];

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (strpos($name, 'ppt/slides/slide') === 0 && str_ends_with($name, '.xml')) {
                        $xml = $zip->getFromIndex($i);
                        $newXml = str_replace(array_keys($replacements), array_values($replacements), $xml);
                        if ($newXml !== $xml) {
                            $zip->addFromString($name, $newXml);
                        }
                    }
                }
                $zip->close();
            }

            $tempPdf = tempnam(sys_get_temp_dir(), 'cert_pdf_') . '.pdf';
            if (file_exists($tempPdf)) {
                @unlink($tempPdf);
            }

            if (function_exists('exec') && windows_os()) {
                $cmd = '$p = New-Object -ComObject PowerPoint.Application; $pres = $p.Presentations.Open("' . $tempFile . '", 2, 2, 0); $pres.SaveAs("' . $tempPdf . '", 32); $pres.Close(); $p.Quit();';
                $enc = base64_encode(mb_convert_encoding($cmd, 'UTF-16LE'));
                @exec("powershell -NoProfile -NonInteractive -EncodedCommand $enc");
            }

            if (file_exists($tempPdf) && filesize($tempPdf) > 0) {
                @unlink($tempFile);
                $filename = "Sertifikat_" . str_replace(' ', '_', $examScore->name) . "_" . $examScore->exam_type . ".pdf";
                return response()->download($tempPdf, $filename, [
                    'Content-Type' => 'application/pdf',
                ])->deleteFileAfterSend(true);
            }

            $filename = "Sertifikat_" . str_replace(' ', '_', $examScore->name) . "_" . $examScore->exam_type . ".pptx";
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ])->deleteFileAfterSend(true);
        }

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
