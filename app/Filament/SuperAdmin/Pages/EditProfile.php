<?php

namespace App\Filament\SuperAdmin\Pages;

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

    protected static string $view = 'filament.super-admin.pages.edit-profile';

    public $photo;

    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $user = Auth::guard('super_admin')->user();
        $this->name = (string) ($user?->name ?? '');
        $this->email = (string) ($user?->email ?? '');
    }

    public function submit(): void
    {
        $user = Auth::guard('super_admin')->user();

        $validated = $this->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:191'],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('super_admins', 'email')->ignore($user?->id),
            ],
        ]);

        if (! $user) {
            return;
        }

        $profilePicturePath = $user->profile_picture;
        if ($this->photo) {
            if ($profilePicturePath) {
                Storage::disk('public')->delete($profilePicturePath);
            }

            $profilePicturePath = $this->photo->store('super-admins', 'public');
        }

        $user->update([
            'profile_picture' => $profilePicturePath,
            'name' => $validated['name'],
            'email' => $validated['email'],
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
