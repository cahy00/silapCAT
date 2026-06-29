<?php

namespace App\Filament\Resources\ExamScores\Pages;

use App\Filament\Resources\ExamScores\ExamScoreResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Jobs\ImportExamScoresJob;
use Illuminate\Support\Facades\Auth;

class CreateExamScore extends CreateRecord
{
    protected static string $resource = ExamScoreResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (!empty($data['import_file']) && !empty($data['import_event_id'])) {
            $fileInput = $data['import_file'];
            $eventId = $data['import_event_id'];

            // Extract the TemporaryUploadedFile object (handles both array wrapper and direct object)
            $fileObj = is_array($fileInput) ? array_values($fileInput)[0] : $fileInput;

            // Manually store the file to a permanent location
            $permanentPath = '';
            if (is_object($fileObj) && method_exists($fileObj, 'store')) {
                $permanentPath = $fileObj->store('temp-imports', 'public');
            } elseif (is_string($fileObj)) {
                $permanentPath = $fileObj; // Fallback
            }

            // Dispatch job to parse and import data synchronously
            ImportExamScoresJob::dispatchSync($permanentPath, $eventId, Auth::id());

            Notification::make()
                ->title('Proses Impor Selesai')
                ->body('File Excel telah diproses. Silakan cek hasil akhir pada tabel di bawah atau notifikasi di pojok kanan atas layar.')
                ->success()
                ->send();

            // Return a dummy model so Filament can continue its cycle (which includes saving the uploaded file to disk)
            return new \App\Models\ExamScore();
        }

        // Before proceeding with normal single record creation, clean up import fields
        unset($data['import_file']);
        unset($data['import_event_id']);

        // Otherwise, proceed with normal single record creation
        return parent::handleRecordCreation($data);
    }

    protected function getRedirectUrl(): string
    {
        // If the record has no ID, it was an import, redirect to index
        if (!$this->record?->exists) {
            return $this->getResource()::getUrl('index');
        }
        
        return parent::getRedirectUrl();
    }

    protected function getCreatedNotification(): ?Notification
    {
        // Suppress the default Filament "Created" notification for imports
        if (!$this->record?->exists) {
            return null;
        }

        return parent::getCreatedNotification();
    }
}
