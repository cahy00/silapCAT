<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    public bool $isDraft = false;
    public array $employeeDataToSave = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('saveAsDraft')
                ->label('Simpan sebagai Draft')
                ->color('warning')
                ->action(function () {
                    $this->isDraft = true;
                    $this->save();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load eventEmployees into the 3 multi-select virtual fields
        $employeeData = EventForm::loadEventEmployees($this->record);
        return array_merge($data, $employeeData);
    }

    protected function afterSave(): void
    {
        EventForm::saveEventEmployees($this->record, $this->employeeDataToSave);
    }
}
