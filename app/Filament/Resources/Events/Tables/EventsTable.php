<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Event;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('start_date', 'desc')
            ->emptyStateHeading('Belum Ada Kegiatan')
            ->emptyStateDescription('Daftar kegiatan yang Anda buat akan muncul di sini.')
            ->emptyStateIcon(null)
            ->columns([
                ...EventColumns::make(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('procurement_category')
                    ->label('Kategori Pengadaan')
                    ->options(\App\Models\ProcurementCategory::pluck('name', 'id'))
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('procurementType', function ($q) use ($data) {
                                $q->where('procurement_category_id', $data['value']);
                            });
                        }
                    }),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(\App\Enums\EventStatus::options()),
                \Filament\Tables\Filters\SelectFilter::make('formation_year')
                    ->label('Tahun Pembentukan')
                    ->options(fn() => Event::distinct()->pluck('formation_year', 'formation_year')->filter()->toArray()),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->label('Lihat')
                        ->icon(null)
                        ->color('info'),
                    \Filament\Actions\Action::make('download_pdf')
                        ->label('Cetak PDF')
                        ->icon(null)
                        ->color('success')
                        ->url(fn (Event $record) => route('events.pdf', $record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\Action::make('executive_summary_pdf')
                        ->label('Cetak Executive Summary')
                        ->icon(null)
                        ->color('info')
                        ->url(fn (Event $record) => route('events.executive-summary-pdf', $record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\Action::make('manage_executive_summary')
                        ->label('Kelola Executive Summary')
                        ->icon(null)
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
                        ->mountUsing(fn ($form, Event $record) => $form->fill([
                            'technical_issues' => $record->technical_issues,
                            'executive_notes' => $record->executive_notes,
                            'documentation_photos' => $record->documentation_photos,
                        ]))
                        ->action(function (Event $record, array $data): void {
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
                    \Filament\Actions\Action::make('upload_dokumen')
                        ->label('Upload Dokumen')
                        ->icon(null)
                        ->color('warning')
                        ->form([
                            \Filament\Schemas\Components\Grid::make(2)->schema([
                                \Filament\Forms\Components\FileUpload::make('doc_implementation_report')
                                    ->label('Laporan Pelaksanaan')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_team_decree')
                                    ->label('SK Tim Pelaksana')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_ba_catos')
                                    ->label('BA CATOS')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_institution_announcement')
                                    ->label('Pengumuman Instansi')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(15360)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                            ]),
                        ])
                        ->mountUsing(fn ($form, Event $record) => $form->fill([
                            'doc_implementation_report' => $record->doc_implementation_report,
                            'doc_team_decree' => $record->doc_team_decree,
                            'doc_ba_catos' => $record->doc_ba_catos,
                            'doc_institution_announcement' => $record->doc_institution_announcement,
                        ]))
                        ->action(function (Event $record, array $data): void {
                            $record->update([
                                'doc_implementation_report' => $data['doc_implementation_report'] ?? $record->doc_implementation_report,
                                'doc_team_decree' => $data['doc_team_decree'] ?? $record->doc_team_decree,
                                'doc_ba_catos' => $data['doc_ba_catos'] ?? $record->doc_ba_catos,
                                'doc_institution_announcement' => $data['doc_institution_announcement'] ?? $record->doc_institution_announcement,
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Dokumen berhasil diupload')
                                ->success()
                                ->send();
                        })
                        ->visible(function (Event $record): bool {
                            if (! auth()->user()?->can('Update:Event')) {
                                return false;
                            }
                            $docs = [
                                $record->doc_implementation_report,
                                $record->doc_team_decree,
                                $record->doc_ba_catos,
                                $record->doc_institution_announcement,
                            ];
                            return collect($docs)->filter(fn($doc) => !empty($doc))->count() < 4;
                        }),
                    \Filament\Actions\Action::make('download_zip')
                        ->label('Unduh ZIP Dokumen')
                        ->icon(null)
                        ->color('warning')
                        ->url(fn (Event $record) => route('events.documents-zip', $record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon(null)
                        ->color('gray'),
                    \Filament\Actions\ReplicateAction::make()
                        ->label('Duplikasi Kegiatan')
                        ->icon(null)
                        ->color('info')
                        ->modalHeading('Duplikasi Kegiatan Ini')
                        ->modalDescription('Salin informasi global kegiatan ini menjadi draft baru? Anda tinggal mengubah atau menambahkan titik lokasi dan instansinya saja tanpa perlu ketik dari awal.')
                        ->beforeReplicaSaved(function (Event $replica) {
                            $replica->status = 'draft';
                            $replica->doc_implementation_report = null;
                            $replica->doc_team_decree = null;
                            $replica->doc_ba_catos = null;
                            $replica->doc_institution_announcement = null;
                        })
                        ->after(function (Event $replica) {
                            \Filament\Notifications\Notification::make()
                                ->title('Kegiatan berhasil diduplikasi')
                                ->body('Salinan kegiatan baru telah dibuat dengan status DRAFT.')
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah')
                        ->icon(null)
                        ->color('primary'),
                    \Filament\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon(null),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->size('sm')
                ->button()
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('export_zip_bulk')
                        ->label('Ekspor ZIP Dokumen Terpilih')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->to(route('events.bulk-documents-zip', ['ids' => $ids]));
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
