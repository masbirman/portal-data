<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Listeners\LogAuthenticationActivity;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom logout response to redirect to unified login
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        // Register logout event listener for activity logging
        // Note: Login logging is handled in each panel's Login page
        Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handleLogout']);
    }
}
