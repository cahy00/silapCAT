<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/events/{event}/pdf', function (\App\Models\Event $event) {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.event-report', ['event' => $event]);
    return $pdf->stream("Laporan_Kegiatan_{$event->id}.pdf");
})->name('events.pdf')->middleware(['auth']);

// Public Certificate Routes
Route::get('/sertifikat', [CertificateController::class, 'index'])->name('certificate.index');
Route::post('/sertifikat', [CertificateController::class, 'search'])->name('certificate.search');
Route::get('/sertifikat/download/{examScore}', [CertificateController::class, 'download'])->name('certificate.download');

// Excel Import Template Download
Route::get('/template/exam-score-import', function () {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Import Nilai');

    // Headers (Row 1)
    $headers = ['NIP', 'Nama', 'Jabatan', 'Instansi', 'Tipe Ujian', 'Tanggal Ujian', 'Nilai CAT BKN', 'Nilai Wawancara', 'Catatan'];
    foreach ($headers as $col => $header) {
        $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
        $sheet->setCellValue($colString . '1', $header);
    }

    // Style header row
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
    ];
    $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
    $sheet->getRowDimension(1)->setRowHeight(25);

    // Sample data (Row 2)
    $sampleData = ['197901019200', 'Contoh Nama', 'Contoh Jabatan', 'Contoh Instansi', 'Contoh Tipe Ujian', '23/05/2026', 455, 85, 'Contoh Catatan'];
    foreach ($sampleData as $col => $value) {
        $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
        $sheet->setCellValue($colString . '2', $value);
    }

    // Style sample row
    $sampleStyle = [
        'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
    ];
    $sheet->getStyle('A2:I2')->applyFromArray($sampleStyle);

    // Auto-size columns
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Add data validation for Tipe Ujian column (E)
    $validation = $sheet->getCell('E2')->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setFormula1('"UD_I,UD_II,UPKP"');
    $validation->setAllowBlank(false);
    $validation->setShowDropDown(true);
    $validation->setShowErrorMessage(true);
    $validation->setErrorTitle('Input Tidak Valid');
    $validation->setError('Pilih salah satu: UD_I, UD_II, atau UPKP');
    // Copy validation to rows 3-100
    for ($row = 3; $row <= 100; $row++) {
        $sheet->getCell("E{$row}")->setDataValidation(clone $validation);
    }

    // Add instructions sheet
    $instrSheet = $spreadsheet->createSheet();
    $instrSheet->setTitle('Petunjuk Pengisian');
    $instructions = [
        ['PETUNJUK PENGISIAN TEMPLATE IMPORT NILAI UJIAN'],
        [''],
        ['Kolom', 'Keterangan', 'Wajib?', 'Contoh'],
        ['A - NIP', 'NIP / Nomor Identitas Pegawai', 'Ya', '197901019200'],
        ['B - Nama', 'Nama Lengkap beserta Gelar', 'Ya', 'Budi Santoso, S.Kom'],
        ['C - Jabatan', 'Jabatan Fungsional / Struktural', 'Tidak', 'Analis Kepegawaian Ahli Pertama'],
        ['D - Instansi', 'Nama Instansi / Unit Kerja', 'Tidak', 'Badan Kepegawaian Negara'],
        ['E - Tipe Ujian', 'Jenis Ujian: UD_I / UD_II / UPKP', 'Ya', 'UPKP'],
        ['F - Tanggal Ujian', 'Tanggal pelaksanaan ujian (DD/MM/YYYY)', 'Ya', '23/05/2026'],
        ['G - Nilai CAT BKN', 'Nilai CAT asli dari BKN (skala 0-500)', 'Ya', '410'],
        ['H - Nilai Wawancara', 'Nilai Wawancara Makalah (skala 0-100). Kosongkan jika UD_I.', 'Tidak', '85'],
        ['I - Catatan', 'Keterangan atau catatan tambahan', 'Tidak', 'Catatan opsional'],
        [''],
        ['CATATAN PENTING:'],
        ['1. Hapus baris contoh (baris 2) sebelum mengimpor data asli.'],
        ['2. Kolom Tipe Ujian hanya menerima: UD_I, UD_II, atau UPKP.'],
        ['3. Untuk UD_I, kolom Nilai Wawancara boleh dikosongkan (tidak ada wawancara).'],
        ['4. Sistem akan menghitung Nilai Akhir dan Status Kelulusan secara otomatis.'],
        ['5. Event / Kegiatan dipilih terpisah di halaman impor, tidak perlu ditulis di Excel.'],
    ];
    foreach ($instructions as $rowIdx => $rowData) {
        foreach ($rowData as $colIdx => $value) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $instrSheet->setCellValue($colString . ($rowIdx + 1), $value);
        }
    }
    // Style instruction sheet
    $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]]);
    $instrSheet->mergeCells('A1:D1');
    $instrSheet->getStyle('A3:D3')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
    ]);
    $instrSheet->getStyle('A14')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]]);
    foreach (range('A', 'D') as $col) {
        $instrSheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set active sheet back to template
    $spreadsheet->setActiveSheetIndex(0);

    // Output
    $fileName = 'Template_Import_Nilai_Ujian.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
})->name('template.exam-score-import')->middleware(['auth']);

