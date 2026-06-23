<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class TemplateExportController extends Controller
{
    public function downloadExamScoreTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Nilai');

        // Headers (Row 1)
        $headers = ['NIP', 'Nama', 'Jabatan', 'Instansi', 'Tipe Ujian', 'Tanggal Ujian', 'Nilai CAT BKN', 'Nilai Wawancara', 'Catatan'];
        foreach ($headers as $col => $header) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '1', $header);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Sample data (Row 2)
        $sampleData = ['197901019200', 'Contoh Nama', 'Contoh Jabatan', 'Contoh Instansi', 'Contoh Tipe Ujian', '23/05/2026', 455, 85, 'Contoh Catatan'];
        foreach ($sampleData as $col => $value) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '2', $value);
        }

        // Style sample row
        $sampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:I2')->applyFromArray($sampleStyle);

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add data validation for Tipe Ujian column (E)
        $validation = $sheet->getCell('E2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
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
                $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
                $instrSheet->setCellValue($colString . ($rowIdx + 1), $value);
            }
        }
        // Style instruction sheet
        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]]);
        $instrSheet->mergeCells('A1:D1');
        $instrSheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ]);
        $instrSheet->getStyle('A14')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]]);
        foreach (range('A', 'D') as $col) {
            $instrSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $fileName = 'Template_Import_Nilai_Ujian.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadEmployeeTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Pegawai');

        // Headers (Row 1)
        $headers = ['NIP', 'Nama', 'Jabatan', 'Status'];
        foreach ($headers as $col => $header) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '1', $header);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Sample data (Row 2)
        $sampleData = ['198001012005011001', 'Budi Santoso, S.Kom', 'Analis Kepegawaian Ahli Pertama', 'Koordinator, IT'];
        foreach ($sampleData as $col => $value) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '2', $value);
        }

        // Style sample row
        $sampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:D2')->applyFromArray($sampleStyle);

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT PEGAWAI'],
            [''],
            ['Kolom', 'Keterangan', 'Wajib?', 'Contoh'],
            ['A - NIP', 'NIP / Nomor Identitas Pegawai', 'Ya', '198001012005011001'],
            ['B - Nama', 'Nama Lengkap beserta Gelar', 'Ya', 'Budi Santoso, S.Kom'],
            ['C - Jabatan', 'Jabatan Fungsional / Struktural', 'Tidak', 'Analis Kepegawaian Ahli Pertama'],
            ['D - Status', 'Status/Kompetensi: Koordinator / IT / Pengawas', 'Tidak', 'Koordinator, IT'],
            [''],
            ['CATATAN PENTING:'],
            ['1. Hapus baris contoh (baris 2) sebelum mengimpor data asli.'],
            ['2. Kolom Status dapat diisi lebih dari satu dengan dipisahkan tanda koma (contoh: Koordinator, IT).'],
            ['3. Status yang valid hanya: Koordinator, IT, Pengawas.'],
        ];
        foreach ($instructions as $rowIdx => $rowData) {
            foreach ($rowData as $colIdx => $value) {
                $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
                $instrSheet->setCellValue($colString . ($rowIdx + 1), $value);
            }
        }
        // Style instruction sheet
        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]]);
        $instrSheet->mergeCells('A1:D1');
        $instrSheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ]);
        $instrSheet->getStyle('A9')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]]);
        foreach (range('A', 'D') as $col) {
            $instrSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $fileName = 'Template_Import_Pegawai.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadLocationTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Lokasi');

        // Headers (Row 1)
        $headers = [
            'Nama Lokasi', 'Tipe', 'Kota', 'Alamat', 
            'Jumlah PC', 'Jumlah Ruangan', 'Status Kelayakan', 
            'Nama Surveyor', 'Tgl Mulai Survei (YYYY-MM-DD)', 
            'Tgl Selesai Survei (YYYY-MM-DD)', 'Catatan Survei'
        ];
        foreach ($headers as $col => $header) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '1', $header);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Sample data (Row 2)
        $sampleData = [
            'Kanreg I BKN Yogyakarta', 'bkn', 'Yogyakarta', 'Jl. Magelang',
            '100', '2', 'feasible',
            'Budi, Santoso', '2026-06-01', '2026-06-02', 'Siap digunakan'
        ];
        foreach ($sampleData as $col => $value) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '2', $value);
        }

        // Style sample row
        $sampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:K2')->applyFromArray($sampleStyle);

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT LOKASI & SURVEI'],
            [''],
            ['Kolom', 'Keterangan', 'Wajib?', 'Contoh'],
            ['A - Nama Lokasi', 'Nama lokasi ujian', 'Ya', 'Kanreg I BKN Yogyakarta'],
            ['B - Tipe', 'Tipe: bkn / mandiri_bkn / mandiri_instansi', 'Ya', 'bkn'],
            ['C - Kota', 'Kota lokasi', 'Tidak', 'Yogyakarta'],
            ['D - Alamat', 'Alamat lengkap', 'Tidak', 'Jl. Magelang Km. 7.5'],
            ['E - Jumlah PC', 'Jumlah PC yang tersedia (Angka)', 'Tidak', '100'],
            ['F - Jumlah Ruangan', 'Jumlah Ruangan (Angka)', 'Tidak', '2'],
            ['G - Status Kelayakan', 'Status: feasible / not_feasible / conditional', 'Tidak', 'feasible'],
            ['H - Nama Surveyor', 'Nama surveyor (pisahkan koma jika lebih dr 1)', 'Tidak', 'Andi, Budi'],
            ['I - Tgl Mulai Survei', 'Format YYYY-MM-DD', 'Tidak', '2026-06-01'],
            ['J - Tgl Selesai Survei', 'Format YYYY-MM-DD', 'Tidak', '2026-06-02'],
            ['K - Catatan Survei', 'Catatan tambahan', 'Tidak', 'AC dingin, jaringan stabil'],
            [''],
            ['CATATAN PENTING:'],
            ['1. Hapus baris contoh (baris 2) sebelum mengimpor data asli.'],
            ['2. Pastikan penulisan Tipe dan Status Kelayakan persis seperti contoh di atas.'],
            ['3. Data lokasi akan diperbarui jika Nama Lokasi sudah ada di database.'],
        ];
        foreach ($instructions as $rowIdx => $rowData) {
            foreach ($rowData as $colIdx => $value) {
                $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
                $instrSheet->setCellValue($colString . ($rowIdx + 1), $value);
            }
        }
        // Style instruction sheet
        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]]);
        $instrSheet->mergeCells('A1:D1');
        $instrSheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ]);
        $instrSheet->getStyle('A16')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]]);
        foreach (range('A', 'D') as $col) {
            $instrSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $fileName = 'Template_Import_Lokasi.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadInstitutionTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Instansi');

        // Headers (Row 1)
        $headers = [
            'Nama Instansi', 'Kode Instansi', 'Alamat', 
            'Contact Person', 'No. HP', 'Email'
        ];
        foreach ($headers as $col => $header) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '1', $header);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Sample data (Row 2)
        $sampleData = [
            'Kementerian Komunikasi dan Informatika', 'KOMINFO', 'Jl. Medan Merdeka Barat No. 9',
            'Budi Santoso', '081234567890', 'budi@kominfo.go.id'
        ];
        foreach ($sampleData as $col => $value) {
            $colString = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colString . '2', $value);
        }

        // Style sample row
        $sampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:F2')->applyFromArray($sampleStyle);

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT INSTANSI'],
            [''],
            ['Kolom', 'Keterangan', 'Wajib?', 'Contoh'],
            ['A - Nama Instansi', 'Nama instansi / lembaga', 'Ya', 'Kementerian Komunikasi dan Informatika'],
            ['B - Kode Instansi', 'Kode singkatan / ID unik', 'Tidak', 'KOMINFO'],
            ['C - Alamat', 'Alamat lengkap instansi', 'Tidak', 'Jl. Medan Merdeka Barat No. 9'],
            ['D - Contact Person', 'Nama narahubung PIC instansi', 'Tidak', 'Budi Santoso'],
            ['E - No. HP', 'Nomor HP/Telepon', 'Tidak', '081234567890'],
            ['F - Email', 'Email resmi', 'Tidak', 'budi@kominfo.go.id'],
            [''],
            ['CATATAN PENTING:'],
            ['1. Hapus baris contoh (baris 2) sebelum mengimpor data asli.'],
            ['2. Data instansi akan diperbarui jika Nama Instansi sudah ada di database.'],
        ];
        foreach ($instructions as $rowIdx => $rowData) {
            foreach ($rowData as $colIdx => $value) {
                $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
                $instrSheet->setCellValue($colString . ($rowIdx + 1), $value);
            }
        }
        // Style instruction sheet
        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]]);
        $instrSheet->mergeCells('A1:D1');
        $instrSheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ]);
        $instrSheet->getStyle('A11')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']]]);
        foreach (range('A', 'D') as $col) {
            $instrSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $fileName = 'Template_Import_Instansi.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
