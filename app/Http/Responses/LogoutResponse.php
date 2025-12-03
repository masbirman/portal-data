<?php

namespace App\Http\Responses;

use App\Models\ActivityLog;
use Filament\Facades\Filament;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Get user before logout for logging
        $user = $request->user();
        $panel = Filament::getCurrentPanel()?->getId() ?? 'admin';

        // Log logout activity
        if ($user) {
            ActivityLog::log(
                'logout',
                "User {$user->name} logged out from " . ucfirst($panel) . " Panel",
                $user,
                ['panel' => $panel, 'role' => $user->role ?? 'unknown']
            );
        }

        // Always redirect to main login page (admin panel)
        return redirect()->to('/admin/login');
    }
}
