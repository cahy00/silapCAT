<?php

namespace App\Filament\Resources\Institutions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Informasi Institusi')
                    ->description('Masukkan detail informasi tentang institusi.')
                    ->aside()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Institusi')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('code')
                                ->label('Kode Institusi')
                                ->maxLength(50)
                                ->default(null),
                        ]),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('contact_person')
                                ->label('Kontak Person')
                                ->maxLength(255)
                                ->default(null),
                            TextInput::make('phone')
                                ->label('Telepon')
                                ->tel()
                                ->maxLength(20)
                                ->default(null),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255)
                                ->default(null),
                        ]),
                    ])->columnSpan(12),
            ]);
    }
}
