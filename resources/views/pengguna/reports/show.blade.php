@extends('layouts.admin')

@section('title', 'Detail Laporan Kerusakan - ' . $report->report_code)

@section('page-title', 'Detail Pengaduan & Tindak Lanjut Kerusakan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Status -->
            <div class="card-header p-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="text-muted small">NOMOR TIKET LAPORAN</span>
                    <h4 class="fw-bold font-monospace text-danger mb-0">{{ $report->report_code }}</h4>
                </div>
                <div>
                    @if($report->status === 'baru')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> Baru / Menunggu Penanganan
                        </span>
                    @elseif($report->status === 'diproses')
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-gear-fill me-1"></i> Sedang Ditangani Teknisi
                        </span>
                    @elseif($report->status === 'selesai')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Selesai Diperbaiki
                        </span>
                    @elseif($report->status === 'ditolak')
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-x-circle-fill me-1"></i> Laporan Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Body Detail -->
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Fasilitas & Kategori -->
                    <div class="col-md-6">
                        <label class="small text-muted fw-semibold d-block mb-1">FASILITAS KAMPUS</label>
                        <h5 class="fw-bold text-dark mb-1">{{ $report->facility->name }}</h5>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $report->facility->location }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted fw-semibold d-block mb-1">KATEGORI & TANGGAL</label>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-1">
                            {{ $report->category_label }}
                        </span>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-clock me-1"></i>Dilaporkan: {{ $report->created_at ? $report->created_at->format('d M Y, H:i') : '-' }} WIB
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-0"></div>

                    <!-- Uraian Kerusakan -->
                    <div class="col-12">
                        <label class="small text-muted fw-semibold d-block mb-2">DESKRIPSI KERUSAKAN / KENDALA</label>
                        <div class="p-3 bg-light rounded-3 text-dark">
                            {{ $report->description }}
                        </div>
                    </div>

                    <!-- Foto Bukti Kerusakan -->
                    @if($report->photo_path)
                        <div class="col-12">
                            <label class="small text-muted fw-semibold d-block mb-2">FOTO BUKTI KERUSAKAN</label>
                            <div class="p-2 bg-light rounded-3 d-inline-block border">
                                <a href="{{ asset('storage/' . $report->photo_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Bukti Kerusakan" class="img-fluid rounded-2 shadow-sm" style="max-height: 250px;">
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Catatan Penanganan Petugas -->
                    @if($report->resolution_notes)
                        <div class="col-12">
                            <div class="alert alert-info rounded-3 mb-0">
                                <strong><i class="bi bi-wrench-adjustable me-1"></i> Catatan Tindak Lanjut & Solusi Petugas:</strong>
                                <p class="mb-1 mt-2">{{ $report->resolution_notes }}</p>
                                @if($report->handler)
                                    <div class="small text-muted mt-2 border-top pt-2">
                                        Petugas Penanggung Jawab: <strong>{{ $report->handler->name }}</strong>
                                        @if($report->resolved_at)
                                            &bull; Diselesaikan pada: {{ $report->resolved_at->format('d M Y, H:i') }} WIB
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <a href="{{ route('pengguna.reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Laporan
                </a>
                <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3">
                    <i class="bi bi-printer me-1"></i> Cetak Tiket
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
