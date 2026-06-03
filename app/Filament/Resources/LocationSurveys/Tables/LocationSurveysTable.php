<?php

namespace App\Filament\Resources\LocationSurveys\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationSurveysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('pc_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cctv_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('feasibility_status')
                    ->searchable(),
                TextColumn::make('surveyor_name')
                    ->searchable(),
                TextColumn::make('jadwal_survey')
                    ->label('JADWAL SURVEY')
                    ->html()
                    ->getStateUsing(fn ($record) => $record->id)
                    ->formatStateUsing(function ($record) {
                        $start = $record->survey_start_date ? \Carbon\Carbon::parse($record->survey_start_date)->translatedFormat('d M Y') : '-';
                        $end = $record->survey_end_date ? \Carbon\Carbon::parse($record->survey_end_date)->translatedFormat('d M Y') : '-';
                        
                        return new \Illuminate\Support\HtmlString("
                            <div class='flex flex-col'>
                                <span class='text-sm font-semibold text-gray-900 dark:text-white leading-tight'>{$start} &mdash; {$end}</span>
                            </div>
                        ");
                    }),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
