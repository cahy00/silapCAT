<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\ExamScore;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class ImportExamScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $filePath;
    public int $eventId;
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $eventId, int $userId)
    {
        $this->filePath = $filePath;
        $this->eventId = $eventId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');
        
        if (!$disk->exists($this->filePath)) {
            $this->notifyUser('Gagal Impor', 'File Excel tidak ditemukan atau sudah terhapus.', 'danger');
            return;
        }

        $absolutePath = $disk->path($this->filePath);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        try {
            $rows = [];
            if ($extension === 'xlsx' || $extension === 'xls') {
                $rows = $this->parseExcelFile($absolutePath);
            } else {
                $rows = $this->parseCsvFile($absolutePath);
            }

            $importedCount = 0;

            foreach ($rows as $dataRow) {
                if (count($dataRow) >= 7) {
                    $examType = trim((string) $dataRow[4]);
                    $catScore = floatval($dataRow[6]);
                    $catScaled = $catScore / 5;
                    $interviewScore = isset($dataRow[7]) && trim((string) $dataRow[7]) !== '' ? floatval($dataRow[7]) : null;

                    if ($examType === 'UD_I') {
                        $totalScore = $catScaled;
                        $interviewScore = null; // Override for UD_I
                    } elseif ($examType === 'UD_II') {
                        $totalScore = ($catScaled * 0.6) + (floatval($interviewScore) * 0.4);
                    } else { // UPKP or fallback
                        $totalScore = ($catScaled * 0.5) + (floatval($interviewScore) * 0.5);
                    }

                    $totalScore = round($totalScore, 2);
                    $status = $totalScore >= 70 ? 'Lulus' : 'Tidak Lulus';

                    ExamScore::create([
                        'event_id' => $this->eventId,
                        'employee_number' => trim((string) $dataRow[0]),
                        'name' => trim((string) $dataRow[1]),
                        'position' => isset($dataRow[2]) && trim((string) $dataRow[2]) !== '' ? trim((string) $dataRow[2]) : null,
                        'institution' => isset($dataRow[3]) && trim((string) $dataRow[3]) !== '' ? trim((string) $dataRow[3]) : null,
                        'exam_type' => $examType,
                        'exam_date' => $this->parseDate($dataRow[5] ?? null),
                        'cat_score' => $catScore,
                        'interview_score' => $interviewScore,
                        'total_score' => $totalScore,
                        'status' => $status,
                        'notes' => isset($dataRow[8]) && trim((string) $dataRow[8]) !== '' ? trim((string) $dataRow[8]) : null,
                    ]);
                    $importedCount++;
                }
            }

            // Cleanup the file
            $disk->delete($this->filePath);

            if ($importedCount > 0) {
                $this->notifyUser('Impor Selesai', "Sukses mengimpor {$importedCount} data peserta ujian secara *background*.", 'success');
            } else {
                $this->notifyUser('Impor Gagal', 'Tidak ada data valid yang ditemukan di dalam file. Pastikan data diisi di baris ke-3 ke bawah pada Sheet Pertama.', 'danger');
            }

        } catch (\Exception $e) {
            $this->notifyUser('Terjadi Kesalahan Impor', 'Pesan error: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Send database notification to the user who started the job.
     */
    private function notifyUser(string $title, string $body, string $status): void
    {
        $user = User::find($this->userId);
        if ($user) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->status($status)
                ->sendToDatabase($user);
        }
    }

    /**
     * Parse an Excel (.xlsx/.xls) file into an array of rows (skipping the header).
     */
    private function parseExcelFile(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        // ALWAYS use the first sheet
        $sheet = $spreadsheet->getSheet(0); 
        $rows = [];
        $isFirstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue; // Skip header row
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Skip completely empty rows
            $filtered = array_filter($rowData, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (!empty($filtered)) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Parse a CSV file into an array of rows (skipping the header).
     */
    private function parseCsvFile(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if ($handle === false) return $rows;

        // Detect separator
        $separator = ',';
        $firstLine = fgets($handle);
        if ($firstLine !== false) {
            if (strpos($firstLine, ';') !== false) {
                $separator = ';';
            }
            rewind($handle);
        }

        // Skip header row
        fgetcsv($handle, 1000, $separator);

        while (($dataRow = fgetcsv($handle, 1000, $separator)) !== false) {
            $rows[] = $dataRow;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Parse various date formats into Y-m-d.
     */
    private function parseDate($value): string
    {
        if (empty($value)) return date('Y-m-d');

        $value = trim((string) $value);

        // Handle Excel serial date numbers
        if (is_numeric($value) && intval($value) > 40000) {
            try {
                $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($value));
                return $dateTime->format('Y-m-d');
            } catch (\Exception $e) {
                return date('Y-m-d');
            }
        }

        // Try DD/MM/YYYY format
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        // Try YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Try other common formats
        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }
}
