<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    /**
     * Handle logout event.
     * Note: Login logging is handled in each panel's Login page
     * to capture panel-specific information.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            // Determine panel from current URL
            $panel = $this->detectPanel();

            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'logout',
                'model_type' => get_class($event->user),
                'model_id' => $event->user->id,
                'description' => "User {$event->user->name} logged out from " . ucfirst($panel) . " Panel",
                'properties' => [
                    'panel' => $panel,
                    'role' => $event->user->role ?? 'unknown',
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Detect current panel from URL path.
     */
    protected function detectPanel(): string
    {
        $path = request()->path();

        if (str_starts_with($path, 'wilayah')) {
            return 'wilayah';
        }

        if (str_starts_with($path, 'sekolah')) {
            return 'sekolah';
        }

        return 'admin';
    }
}
