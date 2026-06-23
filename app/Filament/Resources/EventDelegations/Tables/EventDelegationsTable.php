<?php

namespace App\Filament\Resources\EventDelegations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventDelegationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('event.name')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('eventLocation.location.name')
                    ->label('Lokasi Penugasan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User Operator')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Delegasi')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('event_id')
                    ->label('Kegiatan')
                    ->relationship('event', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
