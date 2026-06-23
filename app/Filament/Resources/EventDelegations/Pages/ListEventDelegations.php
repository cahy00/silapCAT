<?php

namespace App\Filament\Resources\EventDelegations\Pages;

use App\Filament\Resources\EventDelegations\EventDelegationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventDelegations extends ListRecords
{
    protected static string $resource = EventDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
