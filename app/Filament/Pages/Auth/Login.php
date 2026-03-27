<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class Login extends \Filament\Pages\Auth\Login
{
    protected static string $view = 'filament.admin.auth.login';

    public function mount(): void
    {
        Auth::guard('super_admin')->logout();

        parent::mount();
    }

    public function getLayout(): string
    {
        return 'filament.admin.auth.layout';
    }

    public function getBrandName(): string
    {
        return trim(strip_tags(Filament::getBrandName()));
    }

    public function getPanelName(): string
    {
        return Filament::getCurrentPanel()->getId() === 'super-admin' ? 'Super Admin' : 'Admin';
    }
}
