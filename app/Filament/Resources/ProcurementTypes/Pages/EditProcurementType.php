<?php

namespace App\Filament\Resources\ProcurementTypes\Pages;

use App\Filament\Resources\ProcurementTypes\ProcurementTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProcurementType extends EditRecord
{
    protected static string $resource = ProcurementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
