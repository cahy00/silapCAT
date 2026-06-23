<?php
namespace App\Filament\Resources\Staffs\Pages;
use App\Filament\Resources\Staffs\StaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListStaffs extends ListRecords
{
    protected static string $resource = StaffResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
