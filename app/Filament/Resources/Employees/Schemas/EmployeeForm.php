<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                \Filament\Schemas\Components\Section::make('Employee Information')
                    ->description('Provide the details of the employee.')
                    ->aside()
                    ->schema([
                        TextInput::make('employee_number')
                            ->label('Employee Number (NIP)')
                            ->required(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        TextInput::make('position')
                            ->label('Position')
                            ->default(null),
                        CheckboxList::make('status')
                            ->label('Roles/Status')
                            ->options([
                                'coordinator' => 'Koordinator',
                                'IT' => 'IT',
                                'supervisor' => 'Pengawas',
                            ])
                            ->required()
                            ->columns(3),
                    ])->columnSpan(12),
            ]);
    }
}
