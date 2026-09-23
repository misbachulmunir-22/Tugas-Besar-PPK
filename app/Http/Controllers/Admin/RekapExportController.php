<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapExportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $locationFilter = $request->get('location');

        $rekapData = $this->calculateRekapData($startDate, $endDate, $locationFilter);
        $locations = Facility::select('location')->distinct()->pluck('location');

        return view('admin.reports.rekap', compact('rekapData', 'startDate', 'endDate', 'locations', 'locationFilter'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $locationFilter = $request->get('location');

        $rekapData = $this->calculateRekapData($startDate, $endDate, $locationFilter);

        $filename = 'Rekap_Okupansi_dan_Kerusakan_Fasilitas_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($rekapData, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel dapat membaca karakter dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['REKAP OKUPANSI & FREKUENSI KERUSAKAN FASILITAS KAMPUS']);
            fputcsv($handle, ['Periode:', $startDate . ' s/d ' . $endDate]);
            fputcsv($handle, ['Tanggal Cetak:', Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []); // Baris kosong

            fputcsv($handle, [
                'No',
                'Kode Fasilitas',
                'Nama Fasilitas',
                'Tipe',
                'Lokasi',
                'Kapasitas',
                'Total Reservasi Disetujui',
                'Total Jam Penggunaan (Jam)',
                'Total Laporan Kerusakan',
                'Laporan Selesai',
                'Laporan Dalam Proses',
                'Status Operasional Terakhir',
            ]);

            $no = 1;
            foreach ($rekapData['facilities'] as $row) {
                fputcsv($handle, [
                    $no++,
                    $row['code'],
                    $row['name'],
                    $row['type_label'],
                    $row['location'],
                    $row['capacity'],
                    $row['total_reservations'],
                    $row['total_hours'],
                    $row['total_reports'],
                    $row['resolved_reports'],
                    $row['in_progress_reports'],
                    ucfirst(str_replace('_', ' ', $row['status'])),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                $rekapData['totals']['total_reservations'],
                $rekapData['totals']['total_hours'],
                $rekapData['totals']['total_reports'],
                $rekapData['totals']['resolved_reports'],
                $rekapData['totals']['in_progress_reports'],
                '',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printPdf(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $locationFilter = $request->get('location');

        $rekapData = $this->calculateRekapData($startDate, $endDate, $locationFilter);

        return view('admin.reports.print', compact('rekapData', 'startDate', 'endDate', 'locationFilter'));
    }

    protected function calculateRekapData(string $startDate, string $endDate, ?string $location = null): array
    {
        $query = Facility::query();

        if ($location) {
            $query->where('location', $location);
        }

        $facilities = $query->orderBy('location', 'asc')->orderBy('name', 'asc')->get();

        $data = [];
        $totalReservationsAll = 0;
        $totalHoursAll = 0;
        $totalReportsAll = 0;
        $totalResolvedAll = 0;
        $totalInProgressAll = 0;

        foreach ($facilities as $facility) {
            // Ambil reservasi disetujui dalam rentang tanggal
            $reservations = Reservation::where('facility_id', $facility->id)
                ->where('status', 'disetujui')
                ->whereBetween('reservation_date', [$startDate, $endDate])
                ->get();

            $totalHours = 0;
            foreach ($reservations as $res) {
                $start = Carbon::parse($res->start_time);
                $end = Carbon::parse($res->end_time);
                $totalHours += $start->diffInMinutes($end) / 60;
            }

            // Ambil laporan kerusakan dalam rentang tanggal
            $reports = DamageReport::where('facility_id', $facility->id)
                ->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->get();

            $resolvedCount = $reports->where('status', 'selesai')->count();
            $inProgressCount = $reports->whereIn('status', ['baru', 'diproses'])->count();

            $totalReservations = $reservations->count();
            $totalReports = $reports->count();

            $totalReservationsAll += $totalReservations;
            $totalHoursAll += $totalHours;
            $totalReportsAll += $totalReports;
            $totalResolvedAll += $resolvedCount;
            $totalInProgressAll += $inProgressCount;

            $data[] = [
                'id' => $facility->id,
                'code' => $facility->code,
                'name' => $facility->name,
                'type_label' => $facility->type_label,
                'location' => $facility->location,
                'capacity' => $facility->capacity,
                'status' => $facility->status,
                'total_reservations' => $totalReservations,
                'total_hours' => round($totalHours, 1),
                'total_reports' => $totalReports,
                'resolved_reports' => $resolvedCount,
                'in_progress_reports' => $inProgressCount,
            ];
        }

        return [
            'facilities' => $data,
            'totals' => [
                'total_reservations' => $totalReservationsAll,
                'total_hours' => round($totalHoursAll, 1),
                'total_reports' => $totalReportsAll,
                'resolved_reports' => $totalResolvedAll,
                'in_progress_reports' => $totalInProgressAll,
            ],
        ];
    }
}
