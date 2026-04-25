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
                TextColumn::make('survey_date')
                    ->date()
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
