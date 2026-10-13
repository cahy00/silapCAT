<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_participants'] = (int)($data['present_count'] ?? 0) + (int)($data['absent_count'] ?? 0);

        if (empty($data['event_location_id']) && !empty($data['event_id'])) {
            $user = auth()->user();
            $locationId = null;
            if ($user && $user->hasRole('operator')) {
                $locationId = \App\Models\EventDelegation::where('user_id', $user->id)
                    ->where('event_id', $data['event_id'])
                    ->value('event_location_id');
            }
            $data['event_location_id'] = $locationId ?: \App\Models\EventLocation::where('event_id', $data['event_id'])->value('id');
        }

        return $data;
    }
}
