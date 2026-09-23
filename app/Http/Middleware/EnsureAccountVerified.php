<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'pengguna' && $user->status !== 'verified') {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($user->status === 'pending') {
                    return redirect()->route('login')->with('warning', 'Akun Anda (' . $user->email . ') berhasil terdaftar namun masih MENUNGGU VERIFIKASI dari Administrator Kampus. Silakan hubungi admin atau tunggu hingga akun diaktivasi.');
                }

                if ($user->status === 'rejected') {
                    $reason = $user->rejection_reason ? ' Alasan: ' . $user->rejection_reason : '';
                    return redirect()->route('login')->with('error', 'Pendaftaran akun Anda DITOLAK oleh Administrator.' . $reason);
                }
            }
        }

        return $next($request);
    }
}
