@extends('layouts.admin')

@section('title', 'Rekapitulasi Okupansi & Kerusakan')

@section('page-title', 'Rekapitulasi Okupansi & Frekuensi Kerusakan Fasilitas')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <p class="text-muted mb-0">
            Laporan statistik penggunaan fasilitas kampus (total okupansi dan jam terpakai) serta frekuensi laporan kerusakan sarana prasarana dalam rentang periode tertentu.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
        <a href="{{ route('admin.rekap.export_csv', ['start_date' => $startDate, 'end_date' => $endDate, 'location' => $locationFilter]) }}" class="btn btn-success rounded-pill px-3 shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Ekspor CSV / Excel
        </a>
        <a href="{{ route('admin.rekap.print', ['start_date' => $startDate, 'end_date' => $endDate, 'location' => $locationFilter]) }}" class="btn btn-outline-dark rounded-pill px-3" target="_blank">
            <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen
        </a>
    </div>
</div>

<!-- Filter Periode & Lokasi -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('admin.rekap.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Tanggal Mulai Periode</label>
                <input type="date" name="start_date" class="form-control bg-light" value="{{ $startDate }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Tanggal Akhir Periode</label>
                <input type="date" name="end_date" class="form-control bg-light" value="{{ $endDate }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Filter Lokasi Gedung</label>
                <select name="location" class="form-select bg-light">
                    <option value="">Semua Lokasi Kampus</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ $locationFilter === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100 rounded-3">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Kartu Ringkasan Metrik Rekapitulasi -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Reservasi Disetujui</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $rekapData['totals']['total_reservations'] }}</h3>
                    <small class="text-success fw-medium">Sesi Kegiatan</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Durasi Penggunaan</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0">{{ $rekapData['totals']['total_hours'] }}</h3>
                    <small class="text-primary fw-medium">Jam Terpakai</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Laporan Kerusakan</span>
                    <h3 class="fw-bold text-danger mt-1 mb-0">{{ $rekapData['totals']['total_reports'] }}</h3>
                    <small class="text-danger fw-medium">{{ $rekapData['totals']['in_progress_reports'] }} Masih Diproses</small>
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-tools fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Perbaikan Selesai</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">{{ $rekapData['totals']['resolved_reports'] }}</h3>
                    <small class="text-muted fw-medium">Tuntas Diperbaiki</small>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-patch-check-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rinci Okupansi & Frekuensi Kerusakan -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom p-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-table me-2 text-primary"></i> Rincian Okupansi per Fasilitas (Periode {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }})
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Kode & Nama Fasilitas</th>
                    <th>Kategori & Lokasi</th>
                    <th class="text-center">Kapasitas</th>
                    <th class="text-center">Total Reservasi</th>
                    <th class="text-center">Durasi (Jam)</th>
                    <th class="text-center">Laporan Kerusakan</th>
                    <th class="text-center">Status Operasional</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapData['facilities'] as $index => $row)
                    <tr>
                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row['name'] }}</div>
                            <small class="badge bg-light text-secondary border font-monospace">{{ $row['code'] }}</small>
                        </td>
                        <td>
                            <div>{{ $row['type_label'] }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $row['location'] }}</small>
                        </td>
                        <td class="text-center">{{ $row['capacity'] }} Org</td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                {{ $row['total_reservations'] }} Sesi
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">{{ $row['total_hours'] }} Jam</span>
                        </td>
                        <td class="text-center">
                            @if($row['total_reports'] > 0)
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" title="Total Laporan">
                                        {{ $row['total_reports'] }} Total
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" title="Selesai Diperbaiki">
                                        {{ $row['resolved_reports'] }} Selesai
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small"><i class="bi bi-check2 text-success me-1"></i>0 Laporan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['status'] === 'aktif')
                                <span class="badge bg-success rounded-pill px-3 py-1">Aktif</span>
                            @elseif($row['status'] === 'dalam_perbaikan')
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Dalam Perbaikan</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3 py-1">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data fasilitas yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="ps-4 text-uppercase">Total Keseluruhan</td>
                    <td class="text-center text-success">{{ $rekapData['totals']['total_reservations'] }} Sesi</td>
                    <td class="text-center text-primary">{{ $rekapData['totals']['total_hours'] }} Jam</td>
                    <td class="text-center text-danger">{{ $rekapData['totals']['total_reports'] }} Laporan ({{ $rekapData['totals']['resolved_reports'] }} Selesai)</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
