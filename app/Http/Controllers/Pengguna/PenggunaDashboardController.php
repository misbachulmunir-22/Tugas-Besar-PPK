<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PenggunaDashboardController
 *
 * Terkait User Story:
 * - US 5: Ringkasan riwayat dan status reservasi pengguna
 * - US 7: Ringkasan status laporan kerusakan pengguna
 */
class PenggunaDashboardController extends Controller
{
    /**
     * Tampilan Dashboard Pengguna
     * US 5: Menampilkan riwayat reservasi terbaru beserta statusnya
     * US 7: Menampilkan status laporan kerusakan terkini yang dikirim pengguna
     */
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'total_reservations' => Reservation::where('user_id', $userId)->count(),
            'approved_reservations' => Reservation::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'pending_reservations' => Reservation::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'total_reports' => DamageReport::where('user_id', $userId)->count(),
        ];

        // US 5: Riwayat reservasi terbaru
        $recentReservations = Reservation::with('facility')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // US 7: Riwayat laporan kerusakan terbaru
        $recentReports = DamageReport::with('facility')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('pengguna.dashboard', compact('stats', 'recentReservations', 'recentReports'));
    }
}
