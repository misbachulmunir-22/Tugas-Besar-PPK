<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_facilities' => Facility::count(),
            'active_facilities' => Facility::where('status', 'aktif')->count(),
            'maintenance_facilities' => Facility::where('status', 'dalam_perbaikan')->count(),
            'total_users' => User::where('role', 'pengguna')->count(),
            'pending_verifications' => User::where('status', 'pending')->count(),
            'total_staff' => User::where('role', 'petugas')->count(),
            'total_reservations' => Reservation::count(),
            'approved_reservations' => Reservation::where('status', 'disetujui')->count(),
            'total_damage_reports' => DamageReport::count(),
            'resolved_damage_reports' => DamageReport::where('status', 'selesai')->count(),
        ];

        $pendingUsers = User::where('status', 'pending')->latest()->take(5)->get();
        $recentReservations = Reservation::with(['user', 'facility'])->latest()->take(5)->get();
        $recentReports = DamageReport::with(['user', 'facility'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'recentReservations', 'recentReports'));
    }
}
