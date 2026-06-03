<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('causer.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('log_name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Aktivitas')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subjek')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
