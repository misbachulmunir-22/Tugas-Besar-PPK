@extends('layouts.app')

@section('title', 'Dashboard Pengguna')

@section('content')
<div class="container py-4">
    <!-- Header Welcome -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-3 fw-bold d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">Halo, {{ auth()->user()->name }}!</h4>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">
                                <i class="bi bi-patch-check-fill me-1"></i> Akun Terverifikasi
                            </span>
                            <span>&bull;</span>
                            <span class="text-capitalize"><i class="bi bi-person-badge me-1"></i>{{ auth()->user()->user_type }}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-card-text me-1"></i>{{ auth()->user()->identity_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('public.facilities') }}" class="btn btn-gradient rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Reservasi Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Total Reservasi Saya</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_reservations'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-calendar2-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Reservasi Disetujui</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">{{ $stats['approved_reservations'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Laporan Kerusakan</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0">{{ $stats['total_reports'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                        <i class="bi bi-tools fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Shortcuts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-range text-primary me-2"></i> Reservasi Terakhir</h6>
                    <a href="{{ route('pengguna.reservations.index') }}" class="small text-decoration-none">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($recentReservations as $res)
                        <div class="p-2 mb-2 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small d-block text-dark">{{ $res->facility->name ?? 'Fasilitas' }}</strong>
                                <span class="text-muted small">{{ $res->reservation_date }} ({{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }})</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $res->status }}</span>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4 mb-0">Belum ada riwayat reservasi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cone-striped text-warning me-2"></i> Laporan Kerusakan Terakhir</h6>
                    <a href="{{ route('pengguna.reports.index') }}" class="small text-decoration-none">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    @forelse($recentReports as $rep)
                        <div class="p-2 mb-2 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small d-block text-dark">{{ $rep->facility->name ?? 'Fasilitas' }}</strong>
                                <span class="text-muted small">{{ $rep->category }}</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $rep->status }}</span>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4 mb-0">Belum ada laporan kerusakan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
