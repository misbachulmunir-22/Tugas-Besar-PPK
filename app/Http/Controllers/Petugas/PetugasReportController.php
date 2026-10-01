<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PetugasReportController
 *
 * Terkait User Story:
 * - US 8: Petugas melihat antrian laporan kerusakan yang menunggu diproses
 * - US 11: Petugas mengubah status laporan (baru/diproses/selesai/ditolak) dan mencantumkan catatan resolusi
 * - US 12: Petugas menandai fasilitas berstatus 'dalam perbaikan' dan mengembalikannya ke 'aktif'
 */
class PetugasReportController extends Controller
{
    /**
     * Antrian dan Daftar Laporan Kerusakan Petugas
     * US 8: Menampilkan antrian laporan kerusakan dengan filter status, kategori, dan fasilitas
     */
    public function index(Request $request): View
    {
        $query = DamageReport::with(['user', 'facility', 'handler']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();
        $facilities = Facility::orderBy('name', 'asc')->get();

        return view('petugas.reports.index', compact('reports', 'facilities'));
    }

    /**
     * Memperbarui Status Laporan & Resolusi Perbaikan
     * US 11: Mengubah status laporan (baru/diproses/selesai/ditolak) beserta catatan resolusi
     * US 12: Opsional sinkronisasi status fasilitas menjadi 'dalam_perbaikan' atau 'aktif'
     */
    public function updateStatus(Request $request, DamageReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai,ditolak'],
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
            'update_facility_status' => ['nullable', 'in:tidak,dalam_perbaikan,aktif'],
        ]);

        $report->status = $validated['status'];
        $report->resolution_notes = $validated['resolution_notes'];
        $report->handled_by = Auth::id();

        if (in_array($validated['status'], ['selesai', 'ditolak'])) {
            $report->resolved_at = now();
        }

        $report->save();

        // US 12: Update status fasilitas terkait jika dipilih oleh petugas
        if (! empty($validated['update_facility_status']) && $validated['update_facility_status'] !== 'tidak') {
            $facility = $report->facility;
            $facility->status = $validated['update_facility_status'];
            $facility->save();
        }

        return back()->with('success', 'Status laporan '.$report->report_code.' berhasil diperbarui.');
    }

    /**
     * Toggle Status Fasilitas (Dalam Perbaikan / Aktif)
     * US 12: Petugas menandai fasilitas berstatus 'dalam perbaikan' terkait laporan yang ditangani dan mengembalikannya ke 'aktif'
     */
    public function toggleFacilityMaintenance(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:aktif,dalam_perbaikan'],
        ]);

        $facility->update(['status' => $validated['status']]);

        $statusText = ($validated['status'] === 'dalam_perbaikan') ? 'DALAM PERBAIKAN' : 'AKTIF / TERSEDIA';

        return back()->with('success', 'Status fasilitas "'.$facility->name.'" berhasil diubah menjadi '.$statusText.'.');
    }
}
