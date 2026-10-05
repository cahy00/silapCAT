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
        $rawState = $this->form->getRawState();
        $this->employeeDataToSave = [
            'employee_koordinator' => $rawState['employee_koordinator'] ?? ($data['employee_koordinator'] ?? []),
            'employee_it' => $rawState['employee_it'] ?? ($data['employee_it'] ?? []),
            'employee_pengawas' => $rawState['employee_pengawas'] ?? ($data['employee_pengawas'] ?? []),
            'eventLocations' => $rawState['eventLocations'] ?? ($data['eventLocations'] ?? []),
        ];
        
        unset($data['employee_koordinator'], $data['employee_it'], $data['employee_pengawas']);

        if (isset($data['eventLocations']) && is_array($data['eventLocations'])) {
            foreach ($data['eventLocations'] as &$loc) {
                unset($loc['koordinator_ids'], $loc['it_ids'], $loc['pengawas_ids']);
            }
            unset($loc);
        }

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
