<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_zip')
                ->label('Unduh ZIP Dokumen')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('warning')
                ->url(fn ($record) => route('events.documents-zip', $record))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('download_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-m-printer')
                ->color('success')
                ->url(fn ($record) => route('events.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
