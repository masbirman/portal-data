<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memvalidasi user memiliki role yang diizinkan.
 * Jika tidak, redirect ke panel yang sesuai dengan role user.
 */
class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Daftar role yang diizinkan
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect ke panel yang sesuai dengan role user
        return $this->redirectToCorrectPanel($user);
    }

    /**
     * Redirect user ke panel yang sesuai dengan role-nya
     */
    protected function redirectToCorrectPanel($user): Response
    {
        return match ($user->role) {
            'super_admin' => redirect('/admin'),
            'admin_wilayah' => redirect('/wilayah'),
            'user_sekolah' => redirect('/sekolah'),
            default => redirect('/'),
        };
    }
}
