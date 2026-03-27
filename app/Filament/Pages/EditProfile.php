<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

class EditProfile extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Edit Profile';

    protected static ?string $navigationGroup = 'Profile Management';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.edit-profile';

    public $photo;

    public string $name = '';

    public string $email = '';

    public ?string $phone_number = null;

    public ?int $age = null;

    public ?string $gender = null;

    public ?string $company_address = null;

    public function mount(): void
    {
        $admin = Auth::guard('admin')->user();

        $this->name = (string) ($admin?->name ?? '');
        $this->email = (string) ($admin?->email ?? '');
        $this->phone_number = $admin?->phone_number;
        $this->age = $admin?->age;
        $this->gender = $admin?->gender;
        $this->company_address = $admin?->company_address;
    }

    public function submit(): void
    {
        $admin = Auth::guard('admin')->user();

        $validated = $this->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:191'],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'gender' => ['nullable', 'string', 'max:32'],
            'company_address' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $admin) {
            return;
        }

        $profilePicturePath = $admin->profile_picture;
        if ($this->photo) {
            if ($profilePicturePath) {
                Storage::disk('public')->delete($profilePicturePath);
            }

            $profilePicturePath = $this->photo->store('admin-profile-photos', 'public');
        }

        $admin->update([
            'profile_picture' => $profilePicturePath,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'company_address' => $validated['company_address'] ?? null,
        ]);

        Notification::make()
            ->title('Profile updated successfully.')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->photo = null;
        $this->mount();
    }
}
