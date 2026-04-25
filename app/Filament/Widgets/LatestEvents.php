<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Event;
use Filament\Tables\Columns\TextColumn;

class LatestEvents extends TableWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Kegiatan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kegiatan')
                    ->searchable(),
                TextColumn::make('procurementType.name')
                    ->label('Jenis Pengadaan')
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('formation_year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
