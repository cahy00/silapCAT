<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('executive_summary_pdf')
                ->label('Cetak Executive Summary (PDF)')
                ->icon('heroicon-m-document-chart-bar')
                ->color('info')
                ->url(fn ($record) => route('events.executive-summary-pdf', $record))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('manage_executive_summary')
                ->label('Kelola Executive Summary')
                ->icon('heroicon-m-pencil-square')
                ->color('primary')
                ->modalHeading('Kelola Catatan & Dokumentasi Executive Summary')
                ->modalDescription('Perbarui catatan kendala teknis, evaluasi umum, serta foto dokumentasi pelaksanaan.')
                ->modalWidth('2xl')
                ->form([
                    \Filament\Forms\Components\Textarea::make('technical_issues')
                        ->label('Kendala Teknis & Mitigasi Lapangan')
                        ->placeholder('Contoh: Tidak ada kendala teknis berarti. Sesi berjalan tertib...')
                        ->rows(3)
                        ->helperText('Kendala perangkat, jaringan, kelistrikan, atau insiden di lapangan beserta mitigasinya.'),
                    \Filament\Forms\Components\Textarea::make('executive_notes')
                        ->label('Catatan Eksekutif / Evaluasi')
                        ->placeholder('Contoh: Pelaksanaan seleksi berjalan tertib dan mematuhi POS CAT BKN...')
                        ->rows(3)
                        ->helperText('Catatan kesimpulan, evaluasi, atau rekomendasi eksekutif.'),
                    \Filament\Forms\Components\FileUpload::make('documentation_photos')
                        ->label('Foto Dokumentasi Pelaksanaan Ujian')
                        ->multiple()
                        ->reorderable()
                        ->directory('events/documentation')
                        ->disk('public')
                        ->image()
                        ->imageEditor()
                        ->maxFiles(8)
                        ->maxSize(10240)
                        ->helperText('Unggah foto suasana registrasi, ruang CAT, arahan panitia, dll (Maks. 8 foto).'),
                ])
                ->mountUsing(fn ($form, $record) => $form->fill([
                    'technical_issues' => $record->technical_issues,
                    'executive_notes' => $record->executive_notes,
                    'documentation_photos' => $record->documentation_photos,
                ]))
                ->action(function ($record, array $data): void {
                    $record->update([
                        'technical_issues' => $data['technical_issues'] ?? null,
                        'executive_notes' => $data['executive_notes'] ?? null,
                        'documentation_photos' => $data['documentation_photos'] ?? null,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Executive Summary diperbarui')
                        ->body('Catatan teknis, evaluasi, dan foto dokumentasi berhasil disimpan.')
                        ->success()
                        ->send();
                }),
            \Filament\Actions\Action::make('download_zip')
                ->label('Unduh ZIP Dokumen')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('warning')
                ->url(fn ($record) => route('events.documents-zip', $record))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('download_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-m-printer')
                ->color('success')
                ->url(fn ($record) => route('events.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
