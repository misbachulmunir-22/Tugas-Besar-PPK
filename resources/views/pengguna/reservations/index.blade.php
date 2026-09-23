@extends('layouts.admin')

@section('title', 'Riwayat Reservasi Saya')

@section('page-title', 'Riwayat & Status Reservasi Fasilitas Saya')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <p class="text-muted mb-0">
            Daftar permohonan peminjaman fasilitas kampus yang telah Anda ajukan. Anda dapat memantau status persetujuan atau membatalkan reservasi jika diperlukan.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('pengguna.reservations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle-fill me-1"></i> Ajukan Reservasi Baru
        </a>
    </div>
</div>

<!-- Filter Reservasi -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('pengguna.reservations.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Status Reservasi</label>
                <select name="status" class="form-select bg-light">
                    <option value="all">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan_pengguna" {{ request('status') === 'dibatalkan_pengguna' ? 'selected' : '' }}>Dibatalkan Saya</option>
                    <option value="dibatalkan_petugas" {{ request('status') === 'dibatalkan_petugas' ? 'selected' : '' }}>Dibatalkan Petugas</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Filter Tanggal Reservasi</label>
                <input type="date" name="date" class="form-control bg-light" value="{{ request('date') }}">
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">
                    <i class="bi bi-funnel me-1"></i> Filter Data
                </button>
                @if(request()->hasAny(['status', 'date']))
                    <a href="{{ route('pengguna.reservations.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Daftar Reservasi -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Kode Reservasi</th>
                    <th>Fasilitas</th>
                    <th>Jadwal Pelaksanaan</th>
                    <th>Tujuan Kegiatan</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $index => $res)
                    <tr>
                        <td class="ps-4 text-muted">{{ $reservations->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('pengguna.reservations.show', $res) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $res->reservation_code }}
                            </a>
                            <div class="text-muted small">Diajukan: {{ $res->created_at ? $res->created_at->format('d/m/Y H:i') : '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $res->facility->name ?? 'Fasilitas' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $res->facility->location ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1 text-primary"></i>{{ $res->reservation_date->format('d M Y') }}</div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace">
                                <i class="bi bi-clock me-1"></i>{{ $res->formatted_time }} WIB
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark text-truncate" style="max-width: 240px;" title="{{ $res->purpose }}">
                                {{ $res->purpose }}
                            </div>
                            @if($res->rejection_reason)
                                <small class="text-danger d-block"><strong>Alasan Tolak:</strong> {{ $res->rejection_reason }}</small>
                            @endif
                        </td>
                        <td>
                            @if($res->status === 'menunggu')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                </span>
                            @elseif($res->status === 'disetujui')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                </span>
                            @elseif($res->status === 'ditolak')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                </span>
                            @elseif(in_array($res->status, ['dibatalkan_pengguna', 'dibatalkan_petugas']))
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('pengguna.reservations.show', $res) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar2-x fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada permohonan reservasi fasilitas.
                            <div class="mt-2">
                                <a href="{{ route('pengguna.reservations.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-plus-circle me-1"></i> Ajukan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reservations->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reservations->links() }}
        </div>
    @endif
</div>
@endsection
