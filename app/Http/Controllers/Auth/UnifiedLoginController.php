<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UnifiedLoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return $this->redirectToPanel(Auth::user());
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'cf-turnstile-response' => 'required',
        ]);

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Determine if login is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ]);
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();

            // Determine target panel based on user role
            $targetPanel = match ($user->role) {
                'super_admin' => 'admin',
                'admin_wilayah' => 'wilayah',
                'user_sekolah' => 'sekolah',
                default => 'admin',
            };

            // Log successful login
            ActivityLog::log(
                'login',
                "User {$user->name} logged in to " . ucfirst($targetPanel) . " Panel",
                $user,
                ['panel' => $targetPanel, 'role' => $user->role]
            );

            return redirect()->intended("/{$targetPanel}");
        }

        RateLimiter::hit($key, 60);

        throw ValidationException::withMessages([
            'login' => 'Username/Email atau password salah.',
        ]);
    }

    protected function redirectToPanel($user)
    {
        $targetPanel = match ($user->role) {
            'super_admin' => 'admin',
            'admin_wilayah' => 'wilayah',
            'user_sekolah' => 'sekolah',
            default => 'admin',
        };

        return redirect("/{$targetPanel}");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            ActivityLog::log(
                'logout',
                "User {$user->name} logged out",
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
