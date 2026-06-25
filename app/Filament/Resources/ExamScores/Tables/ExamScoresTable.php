<?php

namespace App\Filament\Resources\ExamScores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ExamScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_number')
                    ->label('NIP / Identitas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institution')
                    ->label('Instansi')
                    ->searchable()
                    ->placeholder('Internal')
                    ->sortable(),
                TextColumn::make('event.name')
                    ->label('Event / Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('exam_type')
                    ->label('Jenis Ujian')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'UD_I' => 'info',
                        'UD_II' => 'warning',
                        'UPKP' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'UD_I' => 'Ujian Dinas Tk. I',
                        'UD_II' => 'Ujian Dinas Tk. II',
                        'UPKP' => 'UPKP',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('cat_score')
                    ->label('CAT BKN (Raw)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('interview_score')
                    ->label('Wawancara')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('total_score')
                    ->label('Nilai Akhir')
                    ->numeric()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color(fn ($record) => $record->total_score >= 70 ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lulus' => 'success',
                        'Tidak Lulus' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('exam_date')
                    ->label('Tanggal Ujian')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'name', modifyQueryUsing: function ($query) {
                        $query->with(['eventLocations.eventLocationInstitutions.institution']);
                        $query->whereHas('procurementType', function ($q) {
                            $q->whereIn('name', ['UD', 'UPKP', 'UD/UPKP']);
                        });
                    })
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $institutions = $record->eventLocations
                            ->flatMap(fn($l) => $l->eventLocationInstitutions->map(fn($i) => $i->institution->name ?? ''))
                            ->filter()
                            ->unique()
                            ->implode(', ');
                        $instText = $institutions ? " - {$institutions}" : '';
                        return "{$record->name}{$instText} ({$record->formation_year})";
                    }),
                SelectFilter::make('exam_type')
                    ->label('Jenis Ujian')
                    ->options([
                        'UD_I' => 'Ujian Dinas Tingkat I',
                        'UD_II' => 'Ujian Dinas Tingkat II',
                        'UPKP' => 'UPKP',
                    ]),
                SelectFilter::make('status')
                    ->label('Status Kelulusan')
                    ->options([
                        'Lulus' => 'Lulus',
                        'Tidak Lulus' => 'Tidak Lulus',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('download_certificate')
                    ->label('Sertifikat')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->url(fn ($record) => \Illuminate\Support\Facades\URL::signedRoute('certificate.download', ['examScore' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->status === 'Lulus'),
            ])
            ->toolbarActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\ExamScoreExporter::class)
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('Export Excel')
                    ->color('success'),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\ExportBulkAction::make()
                        ->exporter(\App\Filament\Exports\ExamScoreExporter::class)
                        ->icon('heroicon-o-document-arrow-down')
                        ->label('Export Excel'),
                ]),
            ]);
    }
}
