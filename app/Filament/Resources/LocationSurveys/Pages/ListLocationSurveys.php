<?php

namespace App\Filament\Resources\LocationSurveys\Pages;

use App\Filament\Resources\LocationSurveys\LocationSurveyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLocationSurveys extends ListRecords
{
    protected static string $resource = LocationSurveyResource::class;

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
            'feasible' => Tab::make('Layak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('feasibility_status', 'feasible'))
                ->icon('heroicon-m-check-badge'),
            'not_feasible' => Tab::make('Tidak Layak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('feasibility_status', 'not_feasible'))
                ->icon('heroicon-m-no-symbol'),
            'conditional' => Tab::make('Bersyarat')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('feasibility_status', 'conditional'))
                ->icon('heroicon-m-exclamation-triangle'),
        ];
    }
}
