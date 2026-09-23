<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenggunaDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'total_reservations' => Reservation::where('user_id', $userId)->count(),
            'approved_reservations' => Reservation::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'pending_reservations' => Reservation::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'total_reports' => DamageReport::where('user_id', $userId)->count(),
        ];

        $recentReservations = Reservation::with('facility')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentReports = DamageReport::with('facility')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('pengguna.dashboard', compact('stats', 'recentReservations', 'recentReports'));
    }
}
