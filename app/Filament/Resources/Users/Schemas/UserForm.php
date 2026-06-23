<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Informasi Dasar')
                            ->description('Atur nama dan alamat email pengguna.')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope'),
                            ])->columns(2),

                        \Filament\Schemas\Components\Section::make('Keamanan')
                            ->description(fn (string $context): string => $context === 'create' ? 'Tentukan kata sandi untuk pengguna baru.' : 'Kosongkan jika tidak ingin mengubah kata sandi.')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-key'),
                            ])->columns(1),
                    ])
                    ->columnSpan(['sm' => 12, 'md' => 8]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Otorisasi')
                            ->description('Pilih hak akses untuk pengguna ini.')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                \Filament\Forms\Components\Select::make('roles')
                                    ->label('Hak Akses (Role)')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->live(),
                                \Filament\Forms\Components\Select::make('institution_id')
                                    ->label('Instansi / Unit Kerja')
                                    ->relationship('institution', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->helperText('Pilih instansi jika pengguna ini merupakan Admin Instansi.')
                                    ->visible(function (callable $get) {
                                        $roleIds = $get('roles') ?? [];
                                        if (empty($roleIds)) {
                                            return false;
                                        }
                                        if (in_array('admin_instansi', $roleIds, true) || in_array('admin_instansi', $roleIds, false)) {
                                            return true;
                                        }
                                        return \Spatie\Permission\Models\Role::whereIn('id', $roleIds)
                                            ->where('name', 'admin_instansi')
                                            ->exists();
                                    })
                            ]),
                    ])
                    ->columnSpan(['sm' => 12, 'md' => 4]),
            ])->columns(12);
    }
}
