<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

Route::get('/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }

    $disk = Storage::disk('public');

    if (! $disk->exists($path)) {
        abort(404);
    }

    $fullPath = $disk->path($path);

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where('path', '.*');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::post('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify.post');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.send');

Route::get('/user/login-entry', function (Request $request) {
    Auth::guard('web')->logout();
    Auth::guard('admin')->logout();
    Auth::guard('super_admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('user.login.entry');

Route::get('/admin/login-entry', function (Request $request) {
    Auth::guard('web')->logout();
    Auth::guard('admin')->logout();
    Auth::guard('super_admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/admin/login');
})->name('admin.login.entry');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $services = \App\Models\Service::where('is_active', true)->get();

        return view('user.dashboard', compact('services'));
    })->name('dashboard');

    Route::get('/appointments', function () {
        return view('user.appointments');
    })->name('appointments');

    Route::get('/profile', function (Request $request) {
        return view('user.profile', [
            'user' => $request->user(),
        ]);
    })->name('profile');

    Route::get('/profile/edit', function (Request $request) {
        return view('user.edit-profile', [
            'user' => $request->user(),
        ]);
    })->name('profile.edit');

    Route::get('/profile/password', function (Request $request) {
        return view('user.change-password', [
            'user' => $request->user(),
        ]);
    })->name('profile.password');

    Route::post('/profile', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    })->name('profile.update');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::get('/storage/{path}', function (string $path) {
    $path = ltrim($path, '/');

    if (! preg_match('/\.(?:png|jpe?g|webp|gif|svg)$/i', $path)) {
        abort(404);
    }

    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

Route::get('/media/{path}', function (string $path) {
    $path = ltrim($path, '/');

    if (! preg_match('/\.(?:png|jpe?g|webp|gif|svg)$/i', $path)) {
        abort(404);
    }

    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');
