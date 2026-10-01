<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PetugasReservationController
 *
 * Terkait User Story:
 * - US 8: Petugas melihat antrian reservasi yang masih menunggu diproses
 * - US 9: Petugas menyetujui/menolak reservasi secara manual, sistem mencegah bentrok jadwal otomatis
 * - US 10: Petugas membatalkan reservasi yang sudah disetujui dalam kondisi mendesak dengan alasan pembatalan
 */
class PetugasReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Antrian dan Daftar Reservasi Petugas
     * US 8: Menampilkan antrian reservasi dan filter status/fasilitas/tanggal
     */
    public function index(Request $request): View
    {
        $query = Reservation::with(['user', 'facility', 'approver']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->filled('date')) {
            $query->where('reservation_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_code', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('identity_number', 'like', "%{$search}%");
                    });
            });
        }

        $reservations = $query->latest('reservation_date')->latest('start_time')->paginate(15)->withQueryString();
        $facilities = Facility::orderBy('name', 'asc')->get();

        return view('petugas.reservations.index', compact('reservations', 'facilities'));
    }

    /**
     * Persetujuan Reservasi oleh Petugas
     * US 9: Menyetujui reservasi secara manual & sistem mencegah persetujuan jika bentrok jadwal pada fasilitas sama
     */
    public function approve(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'menunggu') {
            return back()->with('error', 'Hanya permohonan reservasi berstatus "Menunggu" yang dapat disetujui.');
        }

        // US 9: Cek status operasional fasilitas
        if ($reservation->facility->status !== 'aktif') {
            return back()->with('error', 'Persetujuan gagal! Fasilitas "'.$reservation->facility->name.'" saat ini berstatus: '.$reservation->facility->status.'.');
        }

        // US 9: PENCEGAHAN BENTROK JADWAL OTOMATIS:
        // Cek apakah ada jadwal lain yang sudah disetujui pada fasilitas, tanggal, dan rentang waktu yang sama
        if ($this->reservationService->hasConflict(
            $reservation->facility_id,
            $reservation->reservation_date->format('Y-m-d'),
            $reservation->start_time,
            $reservation->end_time,
            $reservation->id
        )) {
            return back()->with('error', 'Sistem mencegah persetujuan! Terdeteksi BENTROK JADWAL dengan reservasi lain yang sudah disetujui pada fasilitas dan slot waktu yang sama.');
        }

        $reservation->update([
            'status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Reservasi '.$reservation->reservation_code.' berhasil DISETUJUI.');
    }

    /**
     * Penolakan Reservasi oleh Petugas
     * US 9: Menolak reservasi secara manual beserta alasan penolakan
     */
    public function reject(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Wajib mencantumkan alasan penolakan reservasi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $reservation->update([
            'status' => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Reservasi '.$reservation->reservation_code.' berhasil DITOLAK.');
    }

    /**
     * Pembatalan Darurat oleh Petugas
     * US 10: Membatalkan reservasi yang sudah disetujui dalam kondisi mendesak dengan alasan pembatalan
     */
    public function emergencyCancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancellation_reason.required' => 'Wajib mencantumkan alasan pembatalan darurat oleh petugas.',
            'cancellation_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $reservation->update([
            'status' => 'dibatalkan_petugas',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return back()->with('success', 'Reservasi '.$reservation->reservation_code.' telah DIBATALKAN oleh Petugas karena alasan mendesak.');
    }
}
