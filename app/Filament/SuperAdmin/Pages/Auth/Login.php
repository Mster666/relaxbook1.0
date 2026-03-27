<?php

namespace App\Filament\SuperAdmin\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    protected static string $view = 'filament.super-admin.auth.login';

    public function mount(): void
    {
        Auth::guard('admin')->logout();

        parent::mount();
    }

    public function getLayout(): string
    {
        return 'filament.admin.auth.layout';
    }

    public function getBrandName(): string
    {
        return 'RelaxBook Super Admin';
    }
}
