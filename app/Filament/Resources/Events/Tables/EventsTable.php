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
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->columns([
                TextColumn::make('name')
                    ->label('INFORMASI KEGIATAN')
                    ->searchable(['name'])
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $name = $record->name;
                        $year = $record->formation_year ? " <span class='ml-2 inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30'>{$record->formation_year}</span>" : "";
                        
                        $institutions = $record->eventInstitutions->map(fn($ei) => 
                            "<div class='flex items-center gap-2 py-0.5'>
                                <div class='w-1 h-1 rounded-full bg-primary-400'></div>
                                <span class='text-sm text-gray-700 dark:text-gray-300 font-medium'>" . e($ei->institution?->name ?? '-') . "</span>
                            </div>"
                        )->join('');

                        $locations = $record->eventLocations->map(fn($el) => 
                            "<div class='flex items-center gap-2 py-0.5 mt-0.5'>
                                <span class='text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider'>Titik Lokasi: " . e($el->location?->name ?? '-') . "</span>
                            </div>"
                        )->join('');
                        
                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-2'>
                                <div class='flex items-center'>
                                    <h3 class='font-black text-lg text-gray-900 dark:text-white uppercase tracking-tighter leading-tight'>{$name}</h3>
                                </div>
                                <div class='flex flex-col gap-0.5 pl-1'>
                                    {$institutions}
                                    {$locations}
                                </div>
                            </div>
                        ");
                    }),

                TextColumn::make('id_jadwal')
                    ->label('JADWAL')
                    ->html()
                    ->getStateUsing(fn (Event $record) => $record->id)
                    ->formatStateUsing(function (Event $record): HtmlString {
                        $startDate = $record->eventInstitutions->min('start_date');
                        $endDate = $record->eventInstitutions->max('end_date');
                        
                        $start = $startDate instanceof \Carbon\Carbon ? $startDate->translatedFormat('d M Y') : ($startDate ?? '-');
                        $end = $endDate instanceof \Carbon\Carbon ? $endDate->translatedFormat('d M Y') : ($endDate ?? '-');
                        
                        $duration = 0;
                        if ($startDate instanceof \Carbon\Carbon && $endDate instanceof \Carbon\Carbon) {
                            $duration = $startDate->diffInDays($endDate) + 1;
                        }

                        return new HtmlString("
                            <div class='flex flex-col py-3 gap-1'>
                                <span class='text-sm font-black text-gray-900 dark:text-white leading-tight'>{$start} — {$end}</span>
                            </div>
                            <div class='flex flex-col py-3 gap-1'>
                                <span class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>{$duration} HARI PELAKSANAAN</span>
                            </div>
                        ");
                    }),

                TextColumn::make('participants_count')
                    ->label('PESERTA')
                    ->html()
                    ->getStateUsing(fn(Event $record) => $record->eventInstitutions->sum('participants_count'))
                    ->formatStateUsing(function ($state): HtmlString {
                        return new HtmlString("
                            <div class='flex flex-col py-3'>
                                <span class='text-lg font-black text-primary-600 dark:text-primary-400 leading-none'>" . number_format($state) . "</span>
                                <span class='text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1'>PESERTA</span>
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
                        ->color('info'),
                    \Filament\Actions\EditAction::make()
                        ->color('primary'),
                    \Filament\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
