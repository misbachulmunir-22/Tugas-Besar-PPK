<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * UserManagementController (Admin)
 *
 * Terkait User Story:
 * - US 13: Admin mendaftarkan akun petugas secara langsung (petugas dilarang registrasi mandiri)
 * - US 14: Admin mendaftarkan akun pengguna (mahasiswa/dosen/staf) secara langsung
 * - US 15: Admin memverifikasi atau menolak akun pengguna hasil registrasi mandiri sebelum akun digunakan login
 */
class UserManagementController extends Controller
{
    /**
     * Daftar Manajemen Pengguna & Tab Antrian Verifikasi
     * US 15: Tab 'pending' untuk verifikasi akun pendaftaran mandiri
     * US 13 & US 14: Tab 'petugas' dan 'pengguna' untuk melihat seluruh akun terdaftar
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'pending'); // pending, petugas, pengguna, admin

        $query = User::query();

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'petugas') {
            $query->where('role', 'petugas');
        } elseif ($tab === 'pengguna') {
            $query->where('role', 'pengguna')->where('status', '!=', 'pending');
        } elseif ($tab === 'admin') {
            $query->where('role', 'admin');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'pending' => User::where('status', 'pending')->count(),
            'petugas' => User::where('role', 'petugas')->count(),
            'pengguna' => User::where('role', 'pengguna')->where('status', '!=', 'pending')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'tab', 'counts'));
    }

    /**
     * Form Tambah Akun Langsung oleh Admin
     * US 13 & US 14: Admin membuka form pendaftaran langsung akun Petugas atau Pengguna
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Simpan Akun Baru oleh Admin
     * US 13: Admin mendaftarkan akun Petugas secara langsung
     * US 14: Admin mendaftarkan akun Pengguna (Mahasiswa/Dosen/Staf) secara langsung tanpa form registrasi mandiri
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:petugas,pengguna,admin'],
            'user_type' => ['required', 'in:mahasiswa,dosen,staf,umum'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran akun wajib ditentukan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        // US 13 & US 14: Akun yang dibuat langsung oleh admin berstatus aktif & verified
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'user_type' => $validated['user_type'],
            'identity_number' => $validated['identity_number'],
            'phone' => $validated['phone'],
            'status' => 'verified',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index', ['tab' => $validated['role']])
            ->with('success', 'Akun '.ucfirst($validated['role']).' baru berhasil didaftarkan langsung oleh Admin.');
    }

    /**
     * Verifikasi Akun Pendaftaran Mandiri
     * US 15: Admin menyetujui/memverifikasi akun pengguna hasil registrasi mandiri sehingga dapat login
     */
    public function verify(User $user): RedirectResponse
    {
        $user->update([
            'status' => 'verified',
            'email_verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Akun pengguna "'.$user->name.'" ('.$user->email.') berhasil DIVERIFIKASI. Pengguna sekarang dapat masuk ke sistem.');
    }

    /**
     * Penolakan Akun Pendaftaran Mandiri
     * US 15: Admin menolak akun pengguna hasil registrasi mandiri beserta alasannya
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Wajib menyertakan alasan penolakan verifikasi.',
        ]);

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Pendaftaran akun "'.$user->name.'" telah DITOLAK.');
    }
}
