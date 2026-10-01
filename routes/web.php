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
| Pemetaan Rute berdasarkan User Story (US 1 - US 17)
*/

// ==========================================
// 1. Rute Publik (Pengunjung & Pengguna)
// ==========================================
// US 1: Melihat jadwal slot 30 menit ketersediaan fasilitas (tanpa detail pemohon/tujuan)
// US 2: Mencari fasilitas berdasarkan tipe, lokasi, dan kapasitas
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/beranda', [PublicController::class, 'index'])->name('public.home');
Route::get('/fasilitas', [PublicController::class, 'facilities'])->name('public.facilities'); // US 2
Route::get('/jadwal', [PublicController::class, 'schedule'])->name('public.schedule'); // US 1

// ==========================================
// 2. Rute Autentikasi (Masuk & Daftar Akun)
// ==========================================
// US 13: Registrasi mandiri hanya untuk Pengguna (petugas tidak melakukan registrasi mandiri)
// US 15: Verifikasi status akun saat login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']); // US 15: Cek status verified/pending/rejected
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register'); // US 13: Hanya pengguna
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// 3. Rute Pengguna Kampus (Mahasiswa / Dosen / Staf)
// ==========================================
// US 3: Mengajukan reservasi pada rentang waktu & tujuan
// US 4: Membatalkan reservasi sendiri sebelum waktu mulai
// US 5: Melihat riwayat dan detail lengkap reservasi
// US 6: Melaporkan kerusakan fasilitas (kategori, deskripsi, foto)
// US 7: Melihat status dan perkembangan laporan kerusakan
Route::middleware(['auth', 'role:pengguna', 'verified_account'])->prefix('pengguna')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])->name('dashboard'); // US 5 & US 7

    // Reservasi Fasilitas
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservations.index'); // US 5
    Route::get('/reservasi/buat', [ReservationController::class, 'create'])->name('reservations.create'); // US 1 & US 3
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservations.store'); // US 3
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservations.show'); // US 5
    Route::post('/reservasi/{reservation}/batal', [ReservationController::class, 'cancel'])->name('reservations.cancel'); // US 4

    // Laporan Kerusakan
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index'); // US 7
    Route::get('/laporan/buat', [ReportController::class, 'create'])->name('reports.create'); // US 6
    Route::post('/laporan', [ReportController::class, 'store'])->name('reports.store'); // US 6
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('reports.show'); // US 7
});

// ==========================================
// 4. Rute Petugas Fasilitas Kampus
// ==========================================
// US 8: Dashboard antrian reservasi & laporan kerusakan menunggu diproses
// US 9: Menyetujui/menolak reservasi manual & pencegahan bentrok jadwal
// US 10: Membatalkan reservasi yang sudah disetujui dalam kondisi mendesak dengan alasan
// US 11: Mengubah status laporan kerusakan beserta catatan resolusi
// US 12: Menandai fasilitas 'dalam perbaikan' dan mengembalikannya ke 'aktif'
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard'); // US 8

    // Manajemen Antrian & Persetujuan Reservasi
    Route::get('/reservasi', [PetugasReservationController::class, 'index'])->name('reservations.index'); // US 8
    Route::post('/reservasi/{reservation}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve'); // US 9
    Route::post('/reservasi/{reservation}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject'); // US 9
    Route::post('/reservasi/{reservation}/emergency-cancel', [PetugasReservationController::class, 'emergencyCancel'])->name('reservations.emergency_cancel'); // US 10

    // Manajemen Laporan Kerusakan & Status Fasilitas
    Route::get('/laporan', [PetugasReportController::class, 'index'])->name('reports.index'); // US 8
    Route::post('/laporan/{report}/status', [PetugasReportController::class, 'updateStatus'])->name('reports.update_status'); // US 11 & US 12
    Route::post('/fasilitas/{facility}/status', [PetugasReportController::class, 'toggleFacilityMaintenance'])->name('facilities.toggle_status'); // US 12
});

// ==========================================
// 5. Rute Administrator Sistem
// ==========================================
// US 13: Mendaftarkan akun petugas secara langsung
// US 14: Mendaftarkan akun pengguna secara langsung
// US 15: Memverifikasi atau menolak akun pengguna hasil registrasi mandiri
// US 16: Mengelola master data fasilitas (tambah/edit/nonaktifkan)
// US 17: Melihat & mengekspor (CSV/PDF) rekap okupansi dan frekuensi kerusakan
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // US 13, 14, 15, 16, 17

    // Master Data Fasilitas
    Route::resource('facilities', FacilityController::class)->except(['show']); // US 16

    // Manajemen Pengguna & Verifikasi Pendaftaran
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index'); // US 13, US 14, US 15
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create'); // US 13, US 14
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store'); // US 13, US 14
    Route::post('/users/{user}/verify', [UserManagementController::class, 'verify'])->name('users.verify'); // US 15
    Route::post('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject'); // US 15

    // Rekapitulasi Okupansi & Frekuensi Kerusakan (Ekspor CSV / PDF)
    Route::get('/rekap', [RekapExportController::class, 'index'])->name('rekap.index'); // US 17
    Route::get('/rekap/export-csv', [RekapExportController::class, 'exportCsv'])->name('rekap.export_csv'); // US 17
    Route::get('/rekap/print', [RekapExportController::class, 'printPdf'])->name('rekap.print'); // US 17
});
