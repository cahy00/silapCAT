<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'mandiri_bkn' => Tab::make('Mandiri BKN')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'mandiri_bkn'))
                ->icon('heroicon-m-building-office'),
            'mandiri_instansi' => Tab::make('Mandiri Instansi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'mandiri_instansi'))
                ->icon('heroicon-m-building-library'),
            'bkn' => Tab::make('BKN')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'bkn'))
                ->icon('heroicon-m-academic-cap'),
        ];
    }
}
