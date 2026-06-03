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
                        $name = $record->name;
                        
                        $institutions = $record->eventInstitutions->map(fn($ei) => 
                            "<div class='flex items-center gap-2 py-0.5'>
                                <div class='w-1 h-1 rounded-full bg-primary-400'></div>
                                <span class='text-sm text-gray-700 dark:text-gray-300 font-medium'>" . e($ei->institution?->name ?? '-') . "</span>
                            </div>"
                        )->join('');

                        $locations = $record->eventLocations->map(fn($el) => 
                            "<div class='flex items-center gap-2 py-0.5 mt-0.5'>
                                <span class='text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded'>LOKASI: " . e($el->location?->name ?? '-') . "</span>
                            </div>"
                        )->join('');
                        
                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-2'>
                                <div>
                                    <h3 class='font-black text-base text-gray-900 dark:text-white uppercase tracking-tighter leading-tight'>{$name}</h3>
                                    <div class='mt-1 text-[10px] font-bold text-primary-600 uppercase tracking-widest'>TAHUN FORMASI: " . ($record->formation_year ?? '-') . "</div>
                                </div>
                                <div class='flex flex-col gap-0.5'>
                                    {$institutions}
                                    <div class='flex flex-wrap gap-1 mt-1'>{$locations}</div>
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('jadwal_pelaksanaan')
                    ->label('JADWAL')
                    ->html()
                    ->getStateUsing(fn (Event $record) => $record->id)
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $locations = $record->eventLocations;
                        
                        if ($locations->isEmpty() || !$locations->first()->start_date) {
                            return new HtmlString("<span class='text-xs italic text-gray-400'>Belum dijadwalkan</span>");
                        }

                        $startDate = $locations->min('start_date');
                        $endDate = $locations->max('end_date');
                        
                        $start = $startDate ? $startDate->translatedFormat('d M Y') : '-';
                        $end = $endDate ? $endDate->translatedFormat('d M Y') : '-';
                        
                        $duration = ($startDate && $endDate) ? $startDate->diffInDays($endDate) + 1 : 0;

                        return new HtmlString("
                            <div class='flex flex-col py-2 gap-1'>
                                <div class='text-sm font-bold text-gray-900 dark:text-white leading-tight'>{$start} &mdash; {$end}</div>
                                <div class='text-[10px] font-black text-primary-600 dark:text-primary-400 tracking-wider uppercase'>{$duration} Hari Kerja / Pelaksanaan</div>
                            </div>
                        ");
                    }),

                TextColumn::make('total_peserta')
                    ->label('PESERTA')
                    ->html()
                    ->getStateUsing(fn(Event $record) => $record->eventLocations->sum('participants_count'))
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
