<?php
namespace App\Filament\Resources\Staffs\Pages;
use App\Filament\Resources\Staffs\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
