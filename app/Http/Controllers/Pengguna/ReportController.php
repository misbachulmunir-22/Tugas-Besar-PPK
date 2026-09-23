<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = DamageReport::with(['facility', 'handler'])
            ->where('user_id', Auth::id());

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        return view('pengguna.reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $facilities = Facility::where('status', '!=', 'nonaktif')->orderBy('name', 'asc')->get();
        $selectedFacilityId = $request->get('facility_id');

        $categories = [
            'fisik_bangunan' => 'Fisik & Bangunan (Pintu, Jendela, Dinding, Plafon)',
            'kelistrikan_elektronik' => 'Kelistrikan & Elektronik (AC, Lampu, Saklar, Kabel)',
            'kebersihan' => 'Kebersihan & Sanitasi Lingkungan',
            'alat_rusak_hilang' => 'Peralatan Rusak / Hilang (Proyektor, PC, Mic, Kursi)',
            'lainnya' => 'Lainnya',
        ];

        return view('pengguna.reports.create', compact('facilities', 'selectedFacilityId', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'category' => ['required', 'in:fisik_bangunan,kelistrikan_elektronik,kebersihan,alat_rusak_hilang,lainnya'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
        ], [
            'facility_id.required' => 'Pilih fasilitas yang mengalami kendala/kerusakan.',
            'category.required' => 'Pilih kategori kerusakan yang relevan.',
            'description.required' => 'Uraikan detail kerusakan atau masalah fasilitas.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter.',
            'photo.image' => 'File bukti harus berupa gambar/foto.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        $reportCode = 'REP-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $report = DamageReport::create([
            'report_code' => $reportCode,
            'user_id' => Auth::id(),
            'facility_id' => $validated['facility_id'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'status' => 'baru',
        ]);

        return redirect()->route('pengguna.reports.show', $report)
            ->with('success', 'Laporan kerusakan berhasil dikirim dengan nomor ' . $reportCode . '. Petugas operasional akan segera menindaklanjuti.');
    }

    public function show(DamageReport $report): View
    {
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $report->load(['facility', 'handler']);

        return view('pengguna.reports.show', compact('report'));
    }
}
