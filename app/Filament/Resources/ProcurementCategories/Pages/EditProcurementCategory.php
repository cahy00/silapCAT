<?php

namespace App\Filament\Resources\ProcurementCategories\Pages;

use App\Filament\Resources\ProcurementCategories\ProcurementCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProcurementCategory extends EditRecord
{
    protected static string $resource = ProcurementCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
