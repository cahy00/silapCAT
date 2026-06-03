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
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Profil Pegawai')
                            ->description('Identitas resmi kepegawaian.')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('employee_number')
                                    ->label('NIP / Nomor Identitas')
                                    ->required()
                                    ->placeholder('19xxxxxxxxxxxxxx')
                                    ->prefixIcon('heroicon-m-credit-card'),
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->placeholder('Nama Beserta Gelar')
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('position')
                                    ->label('Jabatan')
                                    ->placeholder('Contoh: Analis SDM Aparatur')
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 8]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Penugasan')
                            ->description('Keahlian & Peran Spesifik.')
                            ->icon('heroicon-o-star')
                            ->schema([
                                CheckboxList::make('status')
                                    ->label('Status / Kompetensi')
                                    ->options([
                                        'Koordinator' => 'Koordinator',
                                        'IT' => 'Tim IT',
                                        'Pengawas' => 'Pengawas',
                                    ])
                                    ->required()
                                    ->columns(1)
                                    ->bulkToggleable(),
                            ]),
                    ])->columnSpan(['lg' => 4]),
            ]);
    }
}
