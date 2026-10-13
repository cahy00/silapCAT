<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

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

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $sessions = $data['sessions_repeater'] ?? [];
        unset($data['sessions_repeater']);

        $firstRecord = null;
        
        foreach ($sessions as $sessionData) {
            $sessionData['total_participants'] = (int)($sessionData['present_count'] ?? 0) + (int)($sessionData['absent_count'] ?? 0);
            $recordData = array_merge($data, $sessionData);
            
            if (empty($recordData['event_location_id']) && !empty($recordData['event_id'])) {
                $user = auth()->user();
                $locationId = null;
                if ($user && $user->hasRole('operator')) {
                    $locationId = \App\Models\EventDelegation::where('user_id', $user->id)
                        ->where('event_id', $recordData['event_id'])
                        ->value('event_location_id');
                }
                $recordData['event_location_id'] = $locationId ?: \App\Models\EventLocation::where('event_id', $recordData['event_id'])->value('id');
            }
            
            // Cek duplikasi agar tidak terjadi error SQL Unique Constraint
            $exists = static::getModel()::where('event_id', $recordData['event_id'])
                ->where('event_location_id', $recordData['event_location_id'])
                ->where('report_date', $recordData['report_date'])
                ->where('session_name', $recordData['session_name'])
                ->first();
                
            if (!$exists) {
                $record = static::getModel()::create($recordData);
                if (!$firstRecord) {
                    $firstRecord = $record;
                }
            } else {
                if (!$firstRecord) {
                    $firstRecord = $exists;
                }
            }
        }
        
        if (!$firstRecord) {
            $firstRecord = static::getModel()::make($data);
        }

        return $firstRecord;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
