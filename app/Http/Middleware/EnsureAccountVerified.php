<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureAccountVerified
 *
 * Terkait User Story:
 * - US 15: Memverifikasi atau menolak akun pengguna hasil registrasi mandiri sebelum akun dapat login
 */
class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * US 15: Memastikan hanya akun dengan status 'verified' yang diizinkan mengakses fitur pengguna.
     * Akun berstatus 'pending' atau 'rejected' akan ditolak dan diarahkan kembali ke halaman login.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // US 15: Cek status verifikasi untuk role pengguna
            if ($user->role === 'pengguna' && $user->status !== 'verified') {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($user->status === 'pending') {
                    return redirect()->route('login')->with('warning', 'Akun Anda ('.$user->email.') berhasil terdaftar namun masih MENUNGGU VERIFIKASI dari Administrator Kampus. Silakan hubungi admin atau tunggu hingga akun diaktivasi.');
                }

                if ($user->status === 'rejected') {
                    $reason = $user->rejection_reason ? ' Alasan: '.$user->rejection_reason : '';

                    return redirect()->route('login')->with('error', 'Pendaftaran akun Anda DITOLAK oleh Administrator.'.$reason);
                }
            }
        }

        return $next($request);
    }
}
