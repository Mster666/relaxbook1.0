<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ViewProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $navigationGroup = 'Profile Management';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.view-profile';

    public function getUserProperty()
    {
        return Auth::user();
    }
}
