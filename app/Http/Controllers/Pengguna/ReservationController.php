<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index(Request $request): View
    {
        $query = Reservation::with(['facility', 'approver'])
            ->where('user_id', Auth::id());

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('reservation_date', $request->date);
        }

        $reservations = $query->latest('reservation_date')->latest('start_time')->paginate(10)->withQueryString();

        return view('pengguna.reservations.index', compact('reservations'));
    }

    public function create(Request $request): View
    {
        $facilities = Facility::where('status', 'aktif')->orderBy('name', 'asc')->get();
        $selectedFacilityId = $request->get('facility_id');
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));

        $selectedFacility = null;
        $slots = [];

        if ($selectedFacilityId) {
            $selectedFacility = Facility::find($selectedFacilityId);
            if ($selectedFacility) {
                $slots = $this->reservationService->getDailySlots($selectedFacility, $selectedDate);
            }
        }

        return view('pengguna.reservations.create', compact('facilities', 'selectedFacilityId', 'selectedDate', 'selectedFacility', 'slots'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'facility_id.required' => 'Silakan pilih fasilitas yang ingin dipesan.',
            'reservation_date.required' => 'Tanggal reservasi wajib ditentukan.',
            'reservation_date.after_or_equal' => 'Tanggal reservasi tidak boleh tanggal di masa lalu.',
            'start_time.required' => 'Waktu mulai wajib dipilih.',
            'end_time.required' => 'Waktu selesai wajib dipilih.',
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'purpose.required' => 'Tujuan penggunaan fasilitas wajib diisi dengan jelas.',
            'purpose.min' => 'Tujuan penggunaan minimal 10 karakter.',
        ]);

        $facility = Facility::findOrFail($validated['facility_id']);

        // Pastikan fasilitas aktif
        if ($facility->status !== 'aktif') {
            return back()->withInput()->with('error', 'Fasilitas "' . $facility->name . '" saat ini sedang tidak dapat dipesan (Status: ' . $facility->status . ').');
        }

        // Validasi aturan slot 30 menit & jam operasional 07:00-20:00 di server
        $this->reservationService->validateTimeSlot($validated['start_time'], $validated['end_time']);

        // Cek apakah jadwal bentrok dengan reservasi yang sudah disetujui
        if ($this->reservationService->hasConflict($facility->id, $validated['reservation_date'], $validated['start_time'], $validated['end_time'])) {
            return back()->withInput()->with('error', 'Mohon maaf, slot waktu yang Anda pilih sudah terisi dan disetujui untuk pemohon lain. Silakan pilih slot waktu lain.');
        }

        // Generate kode reservasi unik
        $code = 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $reservation = Reservation::create([
            'reservation_code' => $code,
            'user_id' => Auth::id(),
            'facility_id' => $facility->id,
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'purpose' => $validated['purpose'],
            'status' => 'menunggu',
        ]);

        return redirect()->route('pengguna.reservations.show', $reservation)
            ->with('success', 'Reservasi berhasil diajukan dengan kode ' . $code . '! Permohonan Anda akan segera ditinjau oleh Petugas Fasilitas.');
    }

    public function show(Reservation $reservation): View
    {
        // Pastikan hanya pemilik reservasi yang dapat melihat detail lengkap
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat reservasi ini.');
        }

        $reservation->load(['facility', 'approver']);

        return view('pengguna.reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (!$reservation->canBeCancelledByUser()) {
            return back()->with('error', 'Reservasi ini tidak dapat dibatalkan karena sudah lewat dari waktu mulai atau telah ditolak sebelumnya.');
        }

        $reservation->update([
            'status' => 'dibatalkan_pengguna',
            'cancellation_reason' => $request->input('reason', 'Dibatalkan oleh pengguna bersangkutan.'),
        ]);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
