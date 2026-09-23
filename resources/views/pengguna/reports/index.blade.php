@extends('layouts.admin')

@section('title', 'Laporan Kerusakan Fasilitas Saya')

@section('page-title', 'Riwayat & Status Laporan Kerusakan Fasilitas')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <p class="text-muted mb-0">
            Pantau status penanganan dan tindak lanjut laporan kerusakan atau kendala fasilitas kampus yang Anda sampaikan kepada tim operasional sarpras.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('pengguna.reports.create') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Buat Laporan Kerusakan
        </a>
    </div>
</div>

<!-- Filter Laporan -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('pengguna.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label small fw-semibold text-muted">Filter Status Laporan</label>
                <select name="status" class="form-select bg-light">
                    <option value="all">Semua Status Penanganan</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru (Menunggu Tindak Lanjut)</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Sedang Diproses / Diperbaiki</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Sudah Diperbaiki</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">
                    <i class="bi bi-funnel me-1"></i> Filter Data
                </button>
                @if(request()->hasAny(['status']))
                    <a href="{{ route('pengguna.reports.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Kode Laporan</th>
                    <th>Fasilitas</th>
                    <th>Kategori & Deskripsi</th>
                    <th>Status Laporan</th>
                    <th>Tindak Lanjut Petugas</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $rep)
                    <tr>
                        <td class="ps-4 text-muted">{{ $reports->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('pengguna.reports.show', $rep) }}" class="fw-bold font-monospace text-danger text-decoration-none">
                                {{ $rep->report_code }}
                            </a>
                            <div class="text-muted small">{{ $rep->created_at ? $rep->created_at->format('d M Y, H:i') : '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rep->facility->name ?? 'Fasilitas' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $rep->facility->location ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border mb-1">{{ $rep->category_label }}</span>
                            <div class="small text-dark text-truncate" style="max-width: 250px;" title="{{ $rep->description }}">
                                {{ $rep->description }}
                            </div>
                        </td>
                        <td>
                            @if($rep->status === 'baru')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Baru
                                </span>
                            @elseif($rep->status === 'diproses')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-gear-fill me-1"></i> Diproses
                                </span>
                            @elseif($rep->status === 'selesai')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                </span>
                            @elseif($rep->status === 'ditolak')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($rep->resolution_notes)
                                <div class="small text-muted text-truncate" style="max-width: 220px;" title="{{ $rep->resolution_notes }}">
                                    <i class="bi bi-chat-left-text text-primary me-1"></i>{{ $rep->resolution_notes }}
                                </div>
                            @else
                                <span class="text-muted small"><em>Menunggu peninjauan</em></span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('pengguna.reports.show', $rep) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                            Belum ada laporan kerusakan fasilitas yang diajukan.
                            <div class="mt-2">
                                <a href="{{ route('pengguna.reports.create') }}" class="btn btn-warning btn-sm rounded-pill px-3 text-dark fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Lapor Kerusakan
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
