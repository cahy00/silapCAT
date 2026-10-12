<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\Checkbox;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    public function getHeading(): string | Htmlable | null
    {
        return 'Masuk ke Akun Anda';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Portal Resmi Sistem Informasi Laporan Pelaksanaan CAT';
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::auth/pages/login.form.remember.label'))
            ->default(false);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
