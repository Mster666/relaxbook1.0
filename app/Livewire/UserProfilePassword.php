<?php

namespace App\Livewire;

use App\Models\UserLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfilePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        UserLog::record($user, 'password_changed');

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password_status', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.user-profile-password');
    }
}
