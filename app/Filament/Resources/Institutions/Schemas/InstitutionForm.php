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
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Identitas Utama')
                            ->description('Detail dasar identitas institusi.')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Institusi')
                                    ->required()
                                    ->placeholder('Contoh: Kantor Wilayah BKN')
                                    ->prefixIcon('heroicon-m-building-office-2')
                                    ->columnSpan(2),
                                TextInput::make('code')
                                    ->label('Kode Institusi')
                                    ->placeholder('ID-XXXX')
                                    ->prefixIcon('heroicon-m-qr-code')
                                    ->columnSpan(1),
                                \Filament\Forms\Components\Textarea::make('address')
                                    ->label('Alamat Lengkap')
                                    ->placeholder('Jl. Contoh No. 123...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(3),
                    ])->columnSpan(['lg' => 8]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Informasi Kontak')
                            ->description('Penanggung jawab institusi.')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                TextInput::make('contact_person')
                                    ->label('Nama CP')
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone'),
                                TextInput::make('email')
                                    ->label('Email Resmi')
                                    ->email()
                                    ->prefixIcon('heroicon-m-envelope'),
                            ]),
                    ])->columnSpan(['lg' => 4]),
            ]);
    }
}
