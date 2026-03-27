<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
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

    protected static string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Enter Current Password')
                    ->required()
                    ->rule('current_password:admin'),
                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Enter New Password')
                    ->required()
                    ->rule(Password::min(8)->mixedCase()->numbers())
                    ->helperText(new HtmlString('<ul style="margin:0;padding-left:1rem;list-style:disc;">
                        <li>At least 8 characters</li>
                        <li>One uppercase letter</li>
                        <li>One number</li>
                    </ul>'))
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Confirm New Password')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        
        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);
        
        $this->form->fill();

        Notification::make()
            ->title('Password changed successfully.')
            ->success()
            ->send();
    }
}
