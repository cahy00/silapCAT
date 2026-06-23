<?php

namespace App\Filament\Resources\EventDelegations\Pages;

use App\Filament\Resources\EventDelegations\EventDelegationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventDelegation extends CreateRecord
{
    protected static string $resource = EventDelegationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
