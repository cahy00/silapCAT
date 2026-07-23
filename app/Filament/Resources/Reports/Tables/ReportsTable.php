<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.name')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventLocation.location.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('report_date')
                    ->label('Tanggal Laporan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('session_name')
                    ->label('Sesi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Diinput oleh')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_participants')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('present_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('absent_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('highest_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lowest_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->groups([
                \Filament\Tables\Grouping\Group::make('event_location_id')
                    ->label('Kegiatan & Lokasi')
                    ->getTitleFromRecordUsing(fn (\App\Models\Report $record) => ($record->event?->name ?? '-') . ' - ' . ($record->eventLocation?->location?->name ?? '-'))
            ])
            ->defaultGroup('event_location_id')
            ->recordActions([
                \Filament\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (\App\Models\Report $record) => route('reports.pdf', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('exportBulkPdf')
                        ->label('Cetak Rekap PDF Terpilih')
                        ->icon('heroicon-o-printer')
                        ->color('danger')
                        ->openUrlInNewTab()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $ids = $records->pluck('id')->implode(',');
                            $url = route('reports.recap-pdf', ['ids' => $ids]);
                            return redirect()->to($url);
                        }),
                    \Filament\Actions\BulkAction::make('exportBulkExcel')
                        ->label('Ekspor Rekap Excel Terpilih')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->openUrlInNewTab()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $ids = $records->pluck('id')->implode(',');
                            $url = route('reports.recap-excel', ['ids' => $ids]);
                            return redirect()->to($url);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
