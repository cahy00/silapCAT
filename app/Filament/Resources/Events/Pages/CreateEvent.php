<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    public bool $isDraft = false;
    public array $employeeDataToSave = [];

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('saveAsDraft')
                ->label('Simpan sebagai Draft')
                ->color('warning')
                ->action(function () {
                    $this->isDraft = true;
                    $this->create();
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->employeeDataToSave = [
            'employee_koordinator' => $data['employee_koordinator'] ?? [],
            'employee_it' => $data['employee_it'] ?? [],
            'employee_pengawas' => $data['employee_pengawas'] ?? [],
        ];
        
        unset($data['employee_koordinator'], $data['employee_it'], $data['employee_pengawas']);

        if ($this->isDraft) {
            $data['status'] = 'draft';
        } else {
            $data['status'] = EventForm::calculateStatus($data);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        EventForm::saveEventEmployees($this->record, $this->employeeDataToSave);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
