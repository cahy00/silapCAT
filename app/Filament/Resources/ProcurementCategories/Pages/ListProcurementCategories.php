<?php

namespace App\Filament\Resources\ProcurementCategories\Pages;

use App\Filament\Resources\ProcurementCategories\ProcurementCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcurementCategories extends ListRecords
{
    protected static string $resource = ProcurementCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
