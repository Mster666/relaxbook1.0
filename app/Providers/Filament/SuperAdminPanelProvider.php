<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $adminCustomCssPath = public_path('css/admin-custom.css');
        $adminCustomCssUrl = asset('css/admin-custom.css') . (is_file($adminCustomCssPath) ? ('?v=' . filemtime($adminCustomCssPath)) : '');
        $rbLoaderPath = public_path('js/rb-loader.js');
        $rbLoaderUrl = asset('js/rb-loader.js') . (is_file($rbLoaderPath) ? ('?v=' . filemtime($rbLoaderPath)) : '');

        return $panel
            ->id('super-admin')
            ->path('super-admin')
            ->brandName('RelaxBook Super Admin')
            ->favicon(asset('images/logo.png'))
            ->spa()
            ->login(\App\Filament\SuperAdmin\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Rose,
            ])
            ->assets([
                Css::make('admin-custom', $adminCustomCssUrl),
                Js::make('rb-loader', $rbLoaderUrl),
            ])
            ->discoverResources(in: app_path('Filament/SuperAdmin/Resources'), for: 'App\\Filament\\SuperAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\\Filament\\SuperAdmin\\Pages')
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\\Filament\\SuperAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('super_admin');
    }
}
