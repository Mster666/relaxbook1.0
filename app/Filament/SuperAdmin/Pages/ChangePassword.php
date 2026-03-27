<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\HtmlString;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Change Password';

    protected static ?string $navigationGroup = 'Profile Management';

    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.super-admin.pages.change-password';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->password()
                    ->revealable()
                    ->placeholder('Enter Current Password')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->placeholder('Enter New Password')
                    ->required()
                    ->rule(Password::min(8)->mixedCase()->numbers())
                    ->helperText(new HtmlString('<ul style="margin:0;padding-left:1rem;list-style:disc;">
                        <li>At least 8 characters</li>
                        <li>One uppercase letter</li>
                        <li>One number</li>
                    </ul>')),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->placeholder('Confirm New Password')
                    ->same('password')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::guard('super_admin')->user();
        if (! $user || ! Hash::check($data['current_password'] ?? '', $user->password)) {
            Notification::make()->title('Current password is incorrect.')->danger()->send();
            return;
        }
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
        $this->form->fill(['current_password' => null, 'password' => null, 'password_confirmation' => null]);
        Notification::make()->title('Password updated successfully.')->success()->send();
    }
}
