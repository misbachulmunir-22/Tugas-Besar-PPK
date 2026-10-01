<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * AuthController
 *
 * Terkait User Story:
 * - US 13: Menolak pendaftaran petugas secara mandiri (hanya admin yang dapat mendaftarkan petugas)
 * - US 14: Pengguna didaftarkan langsung oleh Admin / melalui alur mandiri jika diaktifkan
 * - US 15: Memeriksa verifikasi akun hasil registrasi mandiri sebelum diizinkan login
 */
class AuthController extends Controller
{
    /**
     * Menampilkan form login
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login
     * US 15: Validasi status akun (pending/rejected/verified) sebelum mengizinkan login
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // US 15: Cek status verifikasi jika role adalah pengguna
            if ($user->role === 'pengguna' && $user->status !== 'verified') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($user->status === 'pending') {
                    return back()->with('warning', 'Pendaftaran akun Anda masih MENUNGGU VERIFIKASI dari Administrator Kampus. Harap tunggu konfirmasi.');
                }

                if ($user->status === 'rejected') {
                    $reason = $user->rejection_reason ? ' Alasan: '.$user->rejection_reason : '';

                    return back()->with('error', 'Pendaftaran akun Anda DITOLAK.'.$reason);
                }
            }

            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Kombinasi email dan password yang Anda masukkan tidak sesuai.',
        ]);
    }

    /**
     * Menampilkan form registrasi mandiri pengguna
     * US 13: Registrasi mandiri HANYA untuk pengguna (mahasiswa/dosen/staf/umum), petugas DILARANG registrasi mandiri
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi mandiri pengguna
     * US 13: Peran otomatis disetel sebagai 'pengguna' (Petugas tidak bisa registrasi mandiri)
     * US 15: Menyiapkan akun pengguna terdaftar
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'user_type' => ['required', 'in:mahasiswa,dosen,staf,umum'],
            'identity_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email kampus wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'identity_number.required' => 'Nomor Identitas (NIM/NIP) wajib diisi.',
            'phone.required' => 'Nomor WhatsApp/Telepon wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // US 13 & US 15: Role terkunci ke 'pengguna' dengan status awal 'pending' menunggu verifikasi Admin
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pengguna',
            'user_type' => $validated['user_type'],
            'identity_number' => $validated['identity_number'],
            'phone' => $validated['phone'],
            'status' => 'pending', // Wajib diverifikasi oleh Administrator sebelum dapat digunakan untuk login
            'email_verified_at' => null,
        ]);

        return redirect()->route('login')
            ->withInput(['email' => $validated['email']])
            ->with('warning', 'Pendaftaran akun mandiri berhasil! Akun Anda saat ini berstatus MENUNGGU VERIFIKASI dari Administrator Kampus. Harap tunggu hingga Admin memverifikasi akun Anda sebelum dapat masuk ke sistem.');
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Redirect dashboard sesuai role pengguna
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'pengguna' => redirect()->route('pengguna.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
