<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RoleMiddleware
 *
 * Terkait User Story:
 * - US 13, 14, 15, 16, 17: Membatasi hak akses rute Administrator ('admin')
 * - US 8, 9, 10, 11, 12: Membatasi hak akses rute Petugas Fasilitas ('petugas')
 * - US 3, 4, 5, 6, 7: Membatasi hak akses rute Pengguna Kampus ('pengguna')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        // Cek apakah role pengguna termasuk dalam role yang diizinkan (admin/petugas/pengguna)
        if (! in_array($user->role, $roles)) {
            abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
        }

        return $next($request);
    }
}
