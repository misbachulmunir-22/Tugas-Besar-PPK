<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        // Cek apakah role pengguna termasuk dalam role yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
        }

        return $next($request);
    }
}
