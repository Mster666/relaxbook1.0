<?php

namespace App\Livewire;

use App\Models\UserLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfile extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $gender;
    public $age;

    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->gender = $user->gender;
        $this->age = $user->age;
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $user->update($validated);
        $changed = array_values(array_diff(array_keys($user->getChanges()), ['updated_at']));
        if ($changed !== []) {
            UserLog::record($user, 'profile_updated', [
                'changed' => $changed,
            ]);
        }

        session()->flash('status', 'Profile updated successfully.');
    }

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
        return view('livewire.user-profile', [
            'user' => Auth::user(),
        ]);
    }
}
