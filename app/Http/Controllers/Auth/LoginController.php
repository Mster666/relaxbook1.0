<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $adminEmails = config('app.admin_emails', []);

        if ($adminEmails !== [] && in_array((string) $credentials['email'], $adminEmails, true)) {
            return back()->withErrors([
                'email' => 'This account can only sign in to the admin panel.',
            ])->onlyInput('email');
        }

        $pending = EmailVerificationProcess::where('email', $credentials['email'])->first();

        if ($pending !== null) {
            $request->session()->put('pending_verification_id', $pending->id);

            return redirect()->route('verification.notice')->withErrors([
                'email' => 'Please verify your email address before logging in.',
            ])->withInput($request->only('email'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
