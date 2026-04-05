<?php

namespace App\Livewire;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfileEdit extends Component
{
    use WithFileUploads;

    protected array $messages = [
        'photo.mimes' => 'Please upload a JPG, JPEG, PNG, or WEBP image.',
        'photo.max' => 'The photo is too large. Please upload an image smaller than 2MB.',
    ];

    public $first_name;

    public $last_name;

    public $email;

    public $phone_number;

    public $gender;

    public $age;

    public $birth_date;

    public $bio;

    public $street_address;

    public $city;

    public $state_province;

    public $zip_code;

    public $country;

    public bool $email_notifications = true;

    public bool $sms_notifications = false;

    public $language;

    public $timezone;

    public $photo;

    public function mount()
    {
        $user = Auth::user();
        $name = trim((string) $user->name);
        $parts = preg_split('/\s+/', $name) ?: [];
        $this->first_name = $parts[0] ?? '';
        $this->last_name = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->gender = $user->gender;
        $this->age = $user->age;
        $this->birth_date = $user->birth_date?->format('Y-m-d');
        $this->bio = $user->bio;
        $this->street_address = $user->street_address;
        $this->city = $user->city;
        $this->state_province = $user->state_province;
        $this->zip_code = $user->zip_code;
        $this->country = $user->country;
        $this->email_notifications = (bool) ($user->email_notifications ?? true);
        $this->sms_notifications = (bool) ($user->sms_notifications ?? false);
        $this->language = $user->language ?? 'English';
        $this->timezone = $user->timezone ?? config('app.timezone', 'Asia/Manila');
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:135'],
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
            'birth_date' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state_province' => ['nullable', 'string', 'max:120'],
            'zip_code' => ['nullable', 'string', 'max:24'],
            'country' => ['nullable', 'string', 'max:120'],
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'language' => ['nullable', 'string', 'max:32'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['name'] = trim($validated['first_name'].' '.($validated['last_name'] ?? ''));
        unset($validated['first_name'], $validated['last_name']);

        if ($this->photo) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $this->photo->store('profile-photos', 'public');
            $validated['profile_picture'] = $path;
        }

        // Remove photo from validated array as it is not in the users table
        unset($validated['photo']);

        $user->update($validated);
        $changed = array_values(array_diff(array_keys($user->getChanges()), ['updated_at']));
        if ($changed !== []) {
            UserLog::record($user, 'profile_updated', [
                'changed' => $changed,
            ]);
        }

        session()->flash('status', 'Profile updated successfully.');
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->photo = null;
        $this->mount();
    }

    public function render()
    {
        return view('livewire.user-profile-edit');
    }
}
