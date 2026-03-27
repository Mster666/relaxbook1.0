<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:email_verification_processes,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $verificationCode = random_int(100000, 999999);

        $pending = EmailVerificationProcess::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'age' => $data['age'],
            'password' => Hash::make($data['password']),
            'verification_code' => $verificationCode,
            'expires_at' => now()->addMinutes(15),
        ]);

        try {
             \Illuminate\Support\Facades\Mail::raw("Your verification code is: {$verificationCode}", function ($message) use ($pending) {
                 $message->to($pending->email)
                         ->subject('Verify your email address');
             });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        $request->session()->put('pending_verification_id', $pending->id);
        return redirect()->route('verification.notice');
    }
}
