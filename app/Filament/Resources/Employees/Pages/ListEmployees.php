<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

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
            'coordinator' => Tab::make('Koordinator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'coordinator'))
                ->icon('heroicon-m-user-group'),
            'IT' => Tab::make('IT')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'IT'))
                ->icon('heroicon-m-cpu-chip'),
            'supervisor' => Tab::make('Pengawas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'supervisor'))
                ->icon('heroicon-m-eye'),
        ];
    }
}
