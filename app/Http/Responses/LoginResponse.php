<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = $request->user();

        $panelId = Filament::getCurrentPanel()?->getId();

        $adminEmails = config('app.admin_emails', []);

        if ($panelId === 'admin') {
            if ($adminEmails !== [] && (! $user || ! in_array((string) $user->email, $adminEmails, true))) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'This account is not allowed to access the admin area.',
                ]);
            }

            return redirect()->to('/admin');
        }

        if ($panelId === 'super-admin') {
            return redirect()->to('/super-admin');
        }

        if (Auth::guard('super_admin')->check()) {
            return redirect()->to('/super-admin');
        }

        return redirect()->to('/admin');
    }
}
