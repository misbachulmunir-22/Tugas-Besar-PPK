@extends('layouts.app')

@section('title', 'Dashboard Petugas Fasilitas')

@section('content')
<div class="container py-4">
    <!-- Header Petugas -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 fs-3 fw-bold d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <span class="badge bg-info text-white rounded-pill px-2">Petugas Fasilitas Kampus</span>
                        <span>&bull;</span>
                        <span><i class="bi bi-card-text me-1"></i>{{ auth()->user()->identity_number }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('petugas.reservations.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-calendar-check me-1"></i> Antrean Reservasi
                </a>
                <a href="{{ route('petugas.reports.index') }}" class="btn btn-outline-warning text-dark rounded-pill px-3">
                    <i class="bi bi-tools me-1"></i> Antrean Kerusakan
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-semibold">Reservasi Menunggu</span>
                <h3 class="fw-bold text-warning mt-1 mb-0">{{ $stats['pending_reservations'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-semibold">Reservasi Hari Ini</span>
                <h3 class="fw-bold text-success mt-1 mb-0">{{ $stats['today_approved_reservations'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-semibold">Laporan Kerusakan Baru</span>
                <h3 class="fw-bold text-danger mt-1 mb-0">{{ $stats['pending_reports'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-semibold">Fasilitas Dalam Perbaikan</span>
                <h3 class="fw-bold text-info mt-1 mb-0">{{ $stats['maintenance_facilities'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-inbox-fill text-warning me-2"></i> Antrean Reservasi Baru</h6>
                    <a href="{{ route('petugas.reservations.index') }}" class="small text-decoration-none">Kelola Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($pendingReservations as $res)
                        <div class="p-2 mb-2 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small d-block text-dark">{{ $res->facility->name ?? 'Fasilitas' }}</strong>
                                <span class="text-muted small">{{ $res->user->name ?? 'Pemohon' }} &bull; {{ $res->reservation_date }}</span>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">Menunggu</span>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4 mb-0">Tidak ada antrean reservasi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Laporan Kerusakan</h6>
                    <a href="{{ route('petugas.reports.index') }}" class="small text-decoration-none">Kelola Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($urgentReports as $rep)
                        <div class="p-2 mb-2 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small d-block text-dark">{{ $rep->facility->name ?? 'Fasilitas' }}</strong>
                                <span class="text-muted small">{{ $rep->category }} &bull; {{ $rep->user->name ?? 'Pelapor' }}</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">{{ $rep->status }}</span>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4 mb-0">Tidak ada antrean laporan kerusakan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
