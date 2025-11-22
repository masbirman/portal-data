<?php

namespace App\Http\Middleware;

use App\Settings\GeneralSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow if site is active
        if (app(GeneralSettings::class)->site_active) {
            return $next($request);
        }

        // Allow if user is logged in (Admin)
        if (auth()->check()) {
            return $next($request);
        }

        // Allow Filament admin routes (to allow login)
        if ($request->is('admin*') || $request->is('livewire*')) {
            return $next($request);
        }

        // Otherwise, show maintenance page
        return response()->view('errors.503', [
            'message' => app(GeneralSettings::class)->maintenance_message
        ], 503);
    }
}
