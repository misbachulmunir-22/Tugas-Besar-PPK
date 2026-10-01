<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PublicController
 *
 * Terkait User Story:
 * - US 1: Menampilkan daftar fasilitas dan status ketersediaan per slot waktu 30 menit (tanpa detail pemohon/tujuan)
 * - US 2: Pencarian dan filter fasilitas berdasarkan tipe, lokasi, dan kapasitas
 */
class PublicController extends Controller
{
    /**
     * Halaman beranda utama publik
     * US 1 & US 2: Ikhtisar statistik dan katalog fasilitas
     */
    public function index(): View
    {
        $stats = [
            'total_facilities' => Facility::where('status', '!=', 'nonaktif')->count(),
            'active_facilities' => Facility::where('status', 'aktif')->count(),
            'total_reservations' => Reservation::where('status', 'disetujui')->count(),
            'today_reservations' => Reservation::where('reservation_date', Carbon::today()->format('Y-m-d'))
                ->where('status', 'disetujui')
                ->count(),
        ];

        $featuredFacilities = Facility::where('status', '!=', 'nonaktif')
            ->orderBy('id', 'asc')
            ->take(6)
            ->get();

        return view('public.home', compact('stats', 'featuredFacilities'));
    }

    /**
     * Katalog dan Pencarian Fasilitas Kampus
     * US 2: Pengunjung/pengguna dapat mencari fasilitas berdasarkan tipe, lokasi, dan kapasitas
     */
    public function facilities(Request $request): View
    {
        $query = Facility::where('status', '!=', 'nonaktif');

        // US 2: Pencarian kata kunci nama, kode, lokasi, atau deskripsi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // US 2: Filter berdasarkan tipe fasilitas
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // US 2: Filter berdasarkan lokasi
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // US 2: Filter berdasarkan kapasitas minimum
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', (int) $request->min_capacity);
        }

        $facilities = $query->orderBy('name', 'asc')->paginate(9)->withQueryString();

        $locations = Facility::select('location')->distinct()->pluck('location');
        $types = [
            'ruang_kelas' => 'Ruang Kelas',
            'aula' => 'Aula / Auditorium',
            'laboratorium' => 'Laboratorium',
            'alat' => 'Peralatan & Media',
            'lapangan' => 'Lapangan Olahraga',
        ];

        return view('public.facilities', compact('facilities', 'locations', 'types'));
    }

    /**
     * Jadwal dan Ketersediaan Fasilitas per Slot Waktu
     * US 1: Melihat status ketersediaan per slot waktu 30 menit (tersedia/tidak tersedia)
     *       tanpa menampilkan informasi identitas pemohon atau tujuan kegiatan
     */
    public function schedule(Request $request, ReservationService $reservationService): View
    {
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedFacilityId = $request->get('facility_id');

        $facilities = Facility::where('status', '!=', 'nonaktif')->orderBy('name', 'asc')->get();

        $selectedFacility = null;
        $slots = [];

        if ($selectedFacilityId) {
            $selectedFacility = Facility::find($selectedFacilityId);
        } elseif ($facilities->isNotEmpty()) {
            $selectedFacility = $facilities->first();
            $selectedFacilityId = $selectedFacility->id;
        }

        if ($selectedFacility) {
            // US 1: Menghasilkan status slot tanpa menyertakan user pemohon/tujuan
            $slots = $reservationService->getDailySlots($selectedFacility, $selectedDate);
        }

        return view('public.schedule', compact('facilities', 'selectedFacility', 'selectedFacilityId', 'selectedDate', 'slots'));
    }
}
