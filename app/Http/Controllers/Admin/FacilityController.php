<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * FacilityController (Admin)
 *
 * Terkait User Story:
 * - US 16: Administrator mengelola data fasilitas (tambah, edit, nonaktifkan/hapus)
 */
class FacilityController extends Controller
{
    /**
     * Daftar Manajemen Fasilitas Admin
     * US 16: Melihat daftar seluruh fasilitas dengan filter status & pencarian
     */
    public function index(Request $request): View
    {
        $query = Facility::query();

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $facilities = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.facilities.index', compact('facilities'));
    }

    /**
     * Form Tambah Fasilitas Baru
     * US 16: Menampilkan form tambah fasilitas
     */
    public function create(): View
    {
        return view('admin.facilities.create');
    }

    /**
     * Simpan Fasilitas Baru
     * US 16: Admin menambahkan data fasilitas baru (nama, kode, tipe, lokasi, kapasitas, foto, status)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:facilities,code'],
            'type' => ['required', 'in:ruang_kelas,aula,laboratorium,alat,lapangan'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'status' => ['required', 'in:aktif,dalam_perbaikan,nonaktif'],
        ], [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'code.required' => 'Kode fasilitas wajib diisi.',
            'code.unique' => 'Kode fasilitas ini sudah digunakan.',
            'type.required' => 'Tipe fasilitas wajib dipilih.',
            'location.required' => 'Lokasi fasilitas wajib diisi.',
            'capacity.required' => 'Kapasitas fasilitas wajib diisi.',
            'status.required' => 'Status operasional wajib dipilih.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('facilities', 'public');
        }

        Facility::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'location' => $validated['location'],
            'capacity' => $validated['capacity'],
            'description' => $validated['description'],
            'photo' => $photoPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas baru berhasil ditambahkan.');
    }

    /**
     * Form Edit Data Fasilitas
     * US 16: Menampilkan form edit fasilitas
     */
    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    /**
     * Perbarui Data Fasilitas
     * US 16: Admin mengedit data fasilitas (nama, tipe, lokasi, kapasitas, status)
     */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:facilities,code,'.$facility->id],
            'type' => ['required', 'in:ruang_kelas,aula,laboratorium,alat,lapangan'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'status' => ['required', 'in:aktif,dalam_perbaikan,nonaktif'],
        ]);

        if ($request->hasFile('photo')) {
            if ($facility->photo && Storage::disk('public')->exists($facility->photo)) {
                Storage::disk('public')->delete($facility->photo);
            }
            $facility->photo = $request->file('photo')->store('facilities', 'public');
        }

        $facility->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'location' => $validated['location'],
            'capacity' => $validated['capacity'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.facilities.index')->with('success', 'Data fasilitas berhasil diperbarui.');
    }

    /**
     * Hapus / Nonaktifkan Fasilitas
     * US 16: Admin menonaktifkan fasilitas jika terdapat riwayat reservasi atau menghapus permanen jika tidak ada relasi
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $hasReservations = $facility->reservations()->exists();

        if ($hasReservations) {
            // US 16: Nonaktifkan fasilitas untuk menjaga integritas data riwayat
            $facility->update(['status' => 'nonaktif']);

            return back()->with('info', 'Fasilitas memiliki data riwayat reservasi, status dialihkan menjadi "Nonaktif".');
        }

        if ($facility->photo && Storage::disk('public')->exists($facility->photo)) {
            Storage::disk('public')->delete($facility->photo);
        }

        $facility->delete();

        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}
