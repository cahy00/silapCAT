<?php

namespace App\Filament\Resources\EventDelegations\Pages;

use App\Filament\Resources\EventDelegations\EventDelegationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventDelegation extends EditRecord
{
    protected static string $resource = EventDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
