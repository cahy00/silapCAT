<?php

namespace App\Filament\Resources\ProcurementTypes\Pages;

use App\Filament\Resources\ProcurementTypes\ProcurementTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcurementTypes extends ListRecords
{
    protected static string $resource = ProcurementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
