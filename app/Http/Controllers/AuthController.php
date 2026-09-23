<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

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

            // Cek status verifikasi jika role adalah pengguna
            if ($user->role === 'pengguna' && $user->status !== 'verified') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($user->status === 'pending') {
                    return back()->with('warning', 'Pendaftaran akun Anda masih MENUNGGU VERIFIKASI dari Administrator Kampus. Harap tunggu konfirmasi.');
                }

                if ($user->status === 'rejected') {
                    $reason = $user->rejection_reason ? ' Alasan: ' . $user->rejection_reason : '';
                    return back()->with('error', 'Pendaftaran akun Anda DITOLAK.' . $reason);
                }
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user)->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Kombinasi email dan password yang Anda masukkan tidak sesuai.',
        ]);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pengguna',
            'user_type' => $validated['user_type'],
            'identity_number' => $validated['identity_number'],
            'phone' => $validated['phone'],
            'status' => 'verified', // Akun langsung aktif & siap login ke dashboard
            'email_verified_at' => now(),
        ]);

        return redirect()->route('login')
            ->withInput(['email' => $validated['email']])
            ->with('success', 'Pendaftaran akun mandiri berhasil! Akun Anda telah terdaftar dan aktif. Silakan masukkan kata sandi Anda dan klik Masuk untuk langsung mengakses Dashboard.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

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
