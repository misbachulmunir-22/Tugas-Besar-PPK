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

/**
 * ReservationController (Pengguna)
 *
 * Terkait User Story:
 * - US 1: Menampilkan slot waktu ketersediaan fasilitas saat reservasi
 * - US 3: Mengajukan reservasi pada rentang waktu tertentu dengan menyebutkan tujuan
 * - US 4: Membatalkan reservasi sendiri sebelum batas waktu kegiatan dimulai
 * - US 5: Melihat riwayat dan detail lengkap reservasi milik pengguna
 */
class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Daftar Riwayat Reservasi Pengguna
     * US 5: Melihat riwayat dan status reservasi (menunggu, disetujui, ditolak, dibatalkan)
     */
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

    /**
     * Form Pengajuan Reservasi Fasilitas
     * US 1 & US 3: Menampilkan jadwal slot 30 menit ketersediaan dan form pemesanan
     */
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
                // US 1: Mengambil ketersediaan slot 30 menit
                $slots = $this->reservationService->getDailySlots($selectedFacility, $selectedDate);
            }
        }

        return view('pengguna.reservations.create', compact('facilities', 'selectedFacilityId', 'selectedDate', 'selectedFacility', 'slots'));
    }

    /**
     * Proses Pengajuan Reservasi Baru
     * US 3: Pengguna mengajukan reservasi pada rentang waktu tertentu dan tujuan penggunaan
     */
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

        // Pastikan fasilitas berstatus aktif
        if ($facility->status !== 'aktif') {
            return back()->withInput()->with('error', 'Fasilitas "'.$facility->name.'" saat ini sedang tidak dapat dipesan (Status: '.$facility->status.').');
        }

        // US 3: Validasi aturan slot 30 menit & jam operasional 07:00-20:00 di server
        $this->reservationService->validateTimeSlot($validated['start_time'], $validated['end_time']);

        // US 3 & US 9: Cek apakah jadwal bentrok dengan reservasi yang sudah disetujui
        if ($this->reservationService->hasConflict($facility->id, $validated['reservation_date'], $validated['start_time'], $validated['end_time'])) {
            return back()->withInput()->with('error', 'Mohon maaf, slot waktu yang Anda pilih sudah terisi dan disetujui untuk pemohon lain. Silakan pilih slot waktu lain.');
        }

        // Generate kode reservasi unik
        $code = 'RSV-'.date('Ymd').'-'.strtoupper(Str::random(5));

        $reservation = Reservation::create([
            'reservation_code' => $code,
            'user_id' => Auth::id(),
            'facility_id' => $facility->id,
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
            'purpose' => $validated['purpose'],
            'status' => 'menunggu',
        ]);

        return redirect()->route('pengguna.reservations.show', $reservation)
            ->with('success', 'Reservasi berhasil diajukan dengan kode '.$code.'! Permohonan Anda akan segera ditinjau oleh Petugas Fasilitas.');
    }

    /**
     * Detail Lengkap Reservasi Pengguna
     * US 5: Pengguna dapat melihat detail lengkap reservasi (fasilitas, waktu, tujuan, approver, alasan penolakan/pembatalan)
     */
    public function show(Reservation $reservation): View
    {
        // Pastikan hanya pemilik reservasi yang dapat melihat detail lengkap
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat reservasi ini.');
        }

        $reservation->load(['facility', 'approver']);

        return view('pengguna.reservations.show', compact('reservation'));
    }

    /**
     * Pembatalan Reservasi Mandiri oleh Pengguna
     * US 4: Pengguna membatalkan reservasinya sendiri sebelum batas waktu tertentu (waktu mulai kegiatan)
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // US 4: Batas waktu pembatalan
        if (! $reservation->canBeCancelledByUser()) {
            return back()->with('error', 'Reservasi ini tidak dapat dibatalkan karena sudah lewat dari waktu mulai atau telah ditolak sebelumnya.');
        }

        $reservation->update([
            'status' => 'dibatalkan_pengguna',
            'cancellation_reason' => $request->input('reason', 'Dibatalkan oleh pengguna bersangkutan.'),
        ]);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
