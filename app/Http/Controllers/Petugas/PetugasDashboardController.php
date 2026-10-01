<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * PetugasDashboardController
 *
 * Terkait User Story:
 * - US 8: Petugas melihat dashboard antrian reservasi dan laporan kerusakan yang menunggu diproses
 */
class PetugasDashboardController extends Controller
{
    /**
     * Dashboard Antrian Operasional Petugas
     * US 8: Menampilkan ringkasan metrik dan daftar antrian permohonan reservasi & laporan yang belum diproses
     */
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        $stats = [
            'pending_reservations' => Reservation::where('status', 'menunggu')->count(),
            'today_approved_reservations' => Reservation::where('reservation_date', $today)->where('status', 'disetujui')->count(),
            'pending_reports' => DamageReport::where('status', 'baru')->count(),
            'in_progress_reports' => DamageReport::where('status', 'diproses')->count(),
            'maintenance_facilities' => Facility::where('status', 'dalam_perbaikan')->count(),
            'total_facilities' => Facility::where('status', '!=', 'nonaktif')->count(),
        ];

        // US 8: Antrian reservasi yang masih menunggu tindakan petugas
        $pendingReservations = Reservation::with(['user', 'facility'])
            ->where('status', 'menunggu')
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        // US 8: Antrian laporan kerusakan yang menunggu atau sedang diproses
        $urgentReports = DamageReport::with(['user', 'facility'])
            ->whereIn('status', ['baru', 'diproses'])
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'pendingReservations', 'urgentReports'));
    }
}
