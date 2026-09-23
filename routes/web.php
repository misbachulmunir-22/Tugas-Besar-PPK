<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\RekapExportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pengguna\PenggunaDashboardController;
use App\Http\Controllers\Pengguna\ReportController;
use App\Http\Controllers\Pengguna\ReservationController;
use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\Petugas\PetugasReportController;
use App\Http\Controllers\Petugas\PetugasReservationController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Reservasi & Pelaporan Fasilitas Kampus (PPK 2026)
|--------------------------------------------------------------------------
*/

// 1. Rute Publik (Beranda Utama, Fasilitas & Jadwal Slot)
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/beranda', [PublicController::class, 'index'])->name('public.home');
Route::get('/fasilitas', [PublicController::class, 'facilities'])->name('public.facilities');
Route::get('/jadwal', [PublicController::class, 'schedule'])->name('public.schedule');

// 2. Rute Autentikasi Masuk & Daftar Akun
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 3. Rute Pengguna (Mahasiswa, Dosen, Staf - Wajib Login & Terverifikasi)
Route::middleware(['auth', 'role:pengguna', 'verified_account'])->prefix('pengguna')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])->name('dashboard');

    // Reservasi
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservasi/buat', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservasi/{reservation}/batal', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Laporan Kerusakan
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/buat', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/laporan', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// 4. Rute Petugas Fasilitas
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Antrian Reservasi
    Route::get('/reservasi', [PetugasReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservasi/{reservation}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservasi/{reservation}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject');
    Route::post('/reservasi/{reservation}/emergency-cancel', [PetugasReservationController::class, 'emergencyCancel'])->name('reservations.emergency_cancel');

    // Manajemen Laporan Kerusakan
    Route::get('/laporan', [PetugasReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan/{report}/status', [PetugasReportController::class, 'updateStatus'])->name('reports.update_status');
    Route::post('/fasilitas/{facility}/status', [PetugasReportController::class, 'toggleFacilityMaintenance'])->name('facilities.toggle_status');
});

// 5. Rute Admin Sistem
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Data Fasilitas
    Route::resource('facilities', FacilityController::class)->except(['show']);

    // Manajemen Pengguna & Verifikasi Pendaftaran
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/verify', [UserManagementController::class, 'verify'])->name('users.verify');
    Route::post('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject');

    // Rekapitulasi & Ekspor
    Route::get('/rekap', [RekapExportController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export-csv', [RekapExportController::class, 'exportCsv'])->name('rekap.export_csv');
    Route::get('/rekap/print', [RekapExportController::class, 'printPdf'])->name('rekap.print');
});
