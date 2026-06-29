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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Kegiatan')
            ->emptyStateDescription('Daftar kegiatan yang Anda buat akan muncul di sini.')
            ->emptyStateIcon(null)
            ->columns([
                TextColumn::make('name')
                    ->label('INFORMASI KEGIATAN')
                    ->searchable(['name'])
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $name = e($record->name);
                        $formationYear = e($record->formation_year ?? '-');
                        
                        $institutions = $record->eventLocations->flatMap(fn($loc) => $loc->eventLocationInstitutions)
                            ->map(fn($ei) => $ei->institution)
                            ->unique('id')
                            ->map(fn($inst) => 
                                "<div class='flex items-center gap-2 py-1'>
                                    <div class='w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400 shrink-0'></div>
                                    <span class='text-sm text-slate-950 dark:text-white font-black tracking-tight leading-snug'>" . e($inst?->name ?? '-') . "</span>
                                </div>"
                            )->join('');

                        $locations = $record->eventLocations->map(fn($el) => 
                            "<div class='py-0.5'>
                                <span class='inline-flex items-center gap-1.5 text-[11px] font-black text-indigo-950 dark:text-indigo-100 bg-indigo-100 dark:bg-indigo-950/80 px-2.5 py-1 rounded-md border border-indigo-300 dark:border-indigo-700 uppercase tracking-wider shadow-2xs'>LOKASI: " . e($el->location?->name ?? '-') . "</span>
                            </div>"
                        )->join('');
                        
                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-2.5'>
                                <div>
                                    <h3 class='font-black text-base text-slate-950 dark:text-white uppercase tracking-tighter leading-tight'>{$name}</h3>
                                    <div class='mt-2'>
                                        <span class='inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-950 dark:bg-purple-950 dark:text-purple-200 border border-purple-300 dark:border-purple-800 uppercase tracking-wider shadow-2xs'>
                                            FORMASI: {$formationYear}
                                        </span>
                                    </div>
                                </div>
                                <div class='flex flex-col gap-1 mt-0.5 pt-2 border-t border-gray-200 dark:border-gray-800'>
                                    {$institutions}
                                    <div class='flex flex-col items-start gap-1.5 mt-0.5'>{$locations}</div>
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('jadwal_pelaksanaan')
                    ->label('JADWAL')
                    ->html()
                    ->getStateUsing(fn (Event $record) => $record->id)
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $startDate = $record->start_date ? \Carbon\Carbon::parse($record->start_date) : null;
                        $endDate = $record->end_date ? \Carbon\Carbon::parse($record->end_date) : null;
                        
                        if (!$startDate || !$endDate) {
                            return new HtmlString("<span class='text-xs italic text-gray-400'>Belum dijadwalkan</span>");
                        }

                        $start = $startDate->translatedFormat('d M Y');
                        $end = $endDate->translatedFormat('d M Y');
                        
                        $duration = $startDate->diffInDays($endDate) + 1;

                        return new HtmlString("
                            <div class='flex flex-col py-2 gap-1'>
                                <div class='text-sm font-bold text-gray-900 dark:text-white leading-tight'>{$start} &mdash; {$end}</div>
                                <div class='text-[10px] font-black text-primary-600 dark:text-primary-400 tracking-wider uppercase'>{$duration} Hari Range Global</div>
                            </div>
                        ");
                    }),

                TextColumn::make('total_peserta')
                    ->label('PESERTA')
                    ->html()
                    ->getStateUsing(fn(Event $record) => $record->eventLocations->flatMap(fn($l) => $l->eventLocationInstitutions)->sum('participants_count'))
                    ->formatStateUsing(function ($state): HtmlString {
                        $count = (int) $state;
                        if ($count === 0) {
                            return new HtmlString("<span class='text-xs italic text-gray-400'>Belum ada data</span>");
                        }
                        
                        return new HtmlString("
                            <div class='flex flex-col py-3'>
                                <div class='flex items-baseline gap-1'>
                                    <span class='text-xl font-black text-primary-600 dark:text-primary-400 leading-none'>" . number_format($count) . "</span>
                                    <span class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>Peserta</span>
                                </div>
                            </div>
                        ");
                    })
                    ->sortable(),

                TextColumn::make('laporan_kehadiran')
                    ->label('LAPORAN & KEHADIRAN')
                    ->html()
                    ->getStateUsing(fn(Event $record) => $record->reports->count())
                    ->formatStateUsing(function (Event $record, $state): HtmlString {
                        $sesiCount = (int) $state;
                        if ($sesiCount === 0) {
                            return new HtmlString("
                                <div class='flex flex-col py-3 gap-1'>
                                    <span class='inline-flex items-center w-fit px-2 py-0.5 rounded-md text-xs font-bold bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400'>0 Sesi Dilaporkan</span>
                                    <span class='text-[10px] italic text-gray-400'>Belum ada rekap harian</span>
                                </div>
                            ");
                        }
                        
                        $pesertaSesi = $record->reports->sum('total_participants');
                        $hadir = $record->reports->sum('present_count');
                        $absen = $record->reports->sum('absent_count');
                        $persen = $pesertaSesi > 0 ? round(($hadir / $pesertaSesi) * 100, 1) : 0;
                        
                        $colorClass = $persen >= 90 
                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400' 
                            : ($persen >= 75 ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400');

                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-1.5 min-w-[150px]'>
                                <div class='flex items-center gap-1.5'>
                                    <span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-black {$colorClass} tracking-wider'>{$persen}% HADIR</span>
                                    <span class='text-[11px] font-bold text-gray-700 dark:text-gray-300'>({$sesiCount} Sesi)</span>
                                </div>
                                <div class='flex items-center gap-2 text-[11px]'>
                                    <span class='text-emerald-600 dark:text-emerald-400 font-bold'>✔ {$hadir} Hadir</span>
                                    <span class='text-gray-300 dark:text-gray-600'>|</span>
                                    <span class='text-rose-600 dark:text-rose-400 font-bold'>✖ {$absen} Absen</span>
                                </div>
                            </div>
                        ");
                    }),

                 TextColumn::make('status_dokumen')
                    ->label('DOKUMEN')
                    ->html()
                    ->getStateUsing(function (Event $record): int {
                        $docs = [
                            $record->doc_implementation_report,
                            $record->doc_team_decree,
                            $record->doc_ba_catos,
                            $record->doc_institution_announcement,
                        ];
                        
                        return collect($docs)->filter(fn($doc) => !empty($doc))->count();
                    })
                    ->formatStateUsing(function (Event $record, $state): HtmlString {
                        $total = 4;
                        $uploadedCount = (int) $state;
                        
                        if ($uploadedCount === $total) {
                            return new HtmlString("
                                <div class='flex flex-col py-3 gap-1'>
                                    <span class='inline-flex items-center w-fit px-2 py-0.5 rounded-md text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 tracking-wider uppercase'>Lengkap</span>
                                    <span class='text-[10px] font-bold text-gray-500 dark:text-gray-400'>{$uploadedCount}/{$total} Dokumen</span>
                                </div>
                            ");
                        }
                        
                        if ($uploadedCount === 0) {
                            return new HtmlString("
                                <div class='flex flex-col py-3 gap-1'>
                                    <span class='inline-flex items-center w-fit px-2 py-0.5 rounded-md text-xs font-black bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-400 tracking-wider uppercase'>Belum Ada</span>
                                    <span class='text-[10px] font-bold text-gray-500 dark:text-gray-400'>0/{$total} Dokumen</span>
                                </div>
                            ");
                        }
                        
                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-1'>
                                <span class='inline-flex items-center w-fit px-2 py-0.5 rounded-md text-xs font-black bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400 tracking-wider uppercase'>Belum Lengkap</span>
                                <span class='text-[10px] font-bold text-gray-500 dark:text-gray-400'>{$uploadedCount}/{$total} Dokumen</span>
                            </div>
                        ");
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
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
                    ->options([
                        'draft' => 'DRAFT',
                        'active' => 'ACTIVE',
                        'completed' => 'COMPLETED',
                        'cancelled' => 'CANCELLED',
                    ]),
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
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_team_decree')
                                    ->label('SK Tim Pelaksana')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_ba_catos')
                                    ->label('BA CATOS')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                                \Filament\Forms\Components\FileUpload::make('doc_institution_announcement')
                                    ->label('Pengumuman Instansi')
                                    ->directory('events/documents')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(5120)
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
                            $docs = [
                                $record->doc_implementation_report,
                                $record->doc_team_decree,
                                $record->doc_ba_catos,
                                $record->doc_institution_announcement,
                            ];
                            return collect($docs)->filter(fn($doc) => !empty($doc))->count() < 4;
                        }),
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
                ->icon(null)
                ->button()
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
