<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Responses\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (! $this->app->runningInConsole() && $this->app->bound('request')) {
            $request = request();
            URL::forceRootUrl($request->getSchemeAndHttpHost());
            if ($request->isSecure()) {
                URL::forceScheme('https');
            }
        }

        Event::listen(Login::class, function (Login $event) {
            if (($event->guard ?? null) !== 'web') {
                return;
            }

            $user = $event->user instanceof User ? $event->user : null;
            UserLog::record($user, 'login', [
                'guard' => $event->guard ?? null,
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (($event->guard ?? null) !== 'web') {
                return;
            }

            $user = $event->user instanceof User ? $event->user : null;
            UserLog::record($user, 'logout', [
                'guard' => $event->guard ?? null,
            ]);
        });
    }
}
