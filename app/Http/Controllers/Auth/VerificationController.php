<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationProcess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function show(Request $request)
    {
        $pendingId = $request->session()->get('pending_verification_id');

        if (! $pendingId) {
            return redirect()->route('register');
        }

        $pending = EmailVerificationProcess::find($pendingId);

        if (! $pending) {
            $request->session()->forget('pending_verification_id');

            return redirect()->route('register');
        }

        return view('auth.verify-email', [
            'email' => $pending->email,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $pendingId = $request->session()->get('pending_verification_id');

        if (! $pendingId) {
            return redirect()->route('register')->withErrors([
                'email' => 'Your verification session has expired. Please register again.',
            ]);
        }

        $pending = EmailVerificationProcess::find($pendingId);

        if (! $pending) {
            $request->session()->forget('pending_verification_id');

            return redirect()->route('register')->withErrors([
                'email' => 'Your verification session has expired. Please register again.',
            ]);
        }

        if ($pending->expires_at !== null && $pending->expires_at->isPast()) {
            return back()->withErrors([
                'code' => 'The verification code has expired. Please request a new code.',
            ]);
        }

        if ($request->code === $pending->verification_code) {
            $user = new User();
            $user->name = $pending->name;
            $user->email = $pending->email;
            $user->phone_number = $pending->phone_number;
            $user->gender = $pending->gender;
            $user->age = $pending->age;
            $user->password = $pending->password;
            $user->email_verified_at = now();
            $user->save();

            $pending->delete();
            $request->session()->forget('pending_verification_id');

            return redirect()->route('login')->with('status', 'Email verified successfully. Please log in to continue.');
        }

        return back()->withErrors(['code' => 'The verification code is incorrect.']);
    }

    public function resend(Request $request)
    {
        $pendingId = $request->session()->get('pending_verification_id');

        if (! $pendingId) {
            return redirect()->route('register')->withErrors([
                'email' => 'Your verification session has expired. Please register again.',
            ]);
        }

        $pending = EmailVerificationProcess::find($pendingId);

        if (! $pending) {
            $request->session()->forget('pending_verification_id');

            return redirect()->route('register')->withErrors([
                'email' => 'Your verification session has expired. Please register again.',
            ]);
        }

        $verificationCode = random_int(100000, 999999);
        $pending->verification_code = $verificationCode;
        $pending->expires_at = now()->addMinutes(15);
        $pending->save();

        try {
            \Illuminate\Support\Facades\Mail::raw("Your new verification code is: {$verificationCode}", function ($message) use ($pending) {
                $message->to($pending->email)
                        ->subject('Verify your email address');
            });
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
             return back()->with('error', 'Failed to send email. Please try again.');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
