<?php

namespace App\Filament\WilayahPanel\Pages\Auth;

use App\Models\ActivityLog;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Support\Facades\Auth;

class Logout extends BasePage
{
    public function mount(): void
    {
        $user = Auth::user();
        
        if ($user) {
            ActivityLog::log(
                'logout',
                "User {$user->name} logged out from Wilayah Panel",
                $user
            );
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        redirect()->route('login')->with('success', 'Anda telah berhasil logout.')->send();
    }
}
