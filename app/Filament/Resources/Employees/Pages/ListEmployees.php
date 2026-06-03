<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Employee;

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
            'all' => Tab::make('Semua Pegawai'),
            'Koordinator' => Tab::make('Koordinator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%Koordinator%'))
                ->badge(Employee::where('status', 'like', '%Koordinator%')->count()),
            'IT' => Tab::make('Tim IT')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%IT%'))
                ->badge(Employee::where('status', 'like', '%IT%')->count()),
            'Pengawas' => Tab::make('Pengawas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%Pengawas%'))
                ->badge(Employee::where('status', 'like', '%Pengawas%')->count()),
        ];
    }
}
