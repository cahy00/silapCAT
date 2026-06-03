<?php

namespace App\Filament\Exports;

use App\Models\ExamScore;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ExamScoreExporter extends Exporter
{
    protected static ?string $model = ExamScore::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee_number')->label('NIP/Identitas'),
            ExportColumn::make('name')->label('Nama Lengkap'),
            ExportColumn::make('position')->label('Jabatan'),
            ExportColumn::make('institution')->label('Instansi'),
            ExportColumn::make('event.name')->label('Event / Kegiatan'),
            ExportColumn::make('exam_type')->label('Jenis Ujian'),
            ExportColumn::make('exam_date')->label('Tanggal Ujian'),
            ExportColumn::make('cat_score')->label('Nilai CAT (Raw)'),
            ExportColumn::make('interview_score')->label('Nilai Wawancara'),
            ExportColumn::make('total_score')->label('Nilai Akhir'),
            ExportColumn::make('status')->label('Status Kelulusan'),
            ExportColumn::make('notes')->label('Catatan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your exam score export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
