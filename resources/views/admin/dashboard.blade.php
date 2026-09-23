@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard Utama Administrator')

@section('content')
<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Pendaftar Pending</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['pending_verifications'] }}</h3>
                    <small class="text-warning fw-medium"><i class="bi bi-clock-history me-1"></i>Perlu Verifikasi</small>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-person-exclamation fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Fasilitas</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_facilities'] }}</h3>
                    <small class="text-success fw-medium">{{ $stats['active_facilities'] }} Fasilitas Aktif</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-building fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Pengguna</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_users'] }}</h3>
                    <small class="text-info fw-medium">{{ $stats['total_staff'] }} Petugas Fasilitas</small>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-people fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Total Reservasi</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_reservations'] }}</h3>
                    <small class="text-success fw-medium">{{ $stats['approved_reservations'] }} Disetujui</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Antrean Pendaftar yang Membutuhkan Verifikasi -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-check-fill text-warning me-2"></i> Pendaftar Menunggu Verifikasi
                </h6>
                <a href="{{ route('admin.users.index', ['tab' => 'pending']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-3">Nama & NIM/NIP</th>
                            <th>Kategori</th>
                            <th>Kontak</th>
                            <th class="text-end pe-3">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingUsers as $user)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->identity_number ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-capitalize">{{ $user->user_type }}</span>
                                </td>
                                <td>
                                    <small class="d-block text-muted">{{ $user->email }}</small>
                                    <small class="text-muted">{{ $user->phone }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Verifikasi pendaftaran pengguna ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-2 py-1" title="Verifikasi">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                                    Tidak ada antrean verifikasi pendaftaran akun saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i> Reservasi Terbaru
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="vstack gap-3">
                    @forelse($recentReservations as $res)
                        <div class="p-2 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small text-dark d-block">{{ $res->facility->name ?? 'Fasilitas' }}</strong>
                                <span class="text-muted small">{{ $res->user->name ?? 'Pemohon' }} &bull; {{ $res->reservation_date }}</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $res->status }}</span>
                        </div>
                    @empty
                        <p class="text-center text-muted small py-4 mb-0">Belum ada aktivitas reservasi terbaru.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
