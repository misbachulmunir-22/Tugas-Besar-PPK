@extends('layouts.admin')

@section('title', 'Manajemen Fasilitas Kampus')

@section('page-title', 'Manajemen Data Fasilitas Kampus')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <p class="text-muted mb-0">
            Kelola data fasilitas kampus, ruang kelas, aula, laboratorium, peralatan, dan lapangan. Pantau status operasional serta ketersediaan sarana prasarana.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Fasilitas Baru
        </a>
    </div>
</div>

<!-- Filter & Pencarian -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('admin.facilities.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Pencarian Fasilitas</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Nama, kode, atau lokasi..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Kategori Fasilitas</label>
                <select name="type" class="form-select bg-light">
                    <option value="all">Semua Kategori</option>
                    <option value="ruang_kelas" {{ request('type') === 'ruang_kelas' ? 'selected' : '' }}>Ruang Kelas</option>
                    <option value="aula" {{ request('type') === 'aula' ? 'selected' : '' }}>Aula / Auditorium</option>
                    <option value="laboratorium" {{ request('type') === 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                    <option value="alat" {{ request('type') === 'alat' ? 'selected' : '' }}>Peralatan & Media</option>
                    <option value="lapangan" {{ request('type') === 'lapangan' ? 'selected' : '' }}>Lapangan Olahraga</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status Operasional</label>
                <select name="status" class="form-select bg-light">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="dalam_perbaikan" {{ request('status') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-3 flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Fasilitas -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Fasilitas</th>
                    <th>Kategori</th>
                    <th>Lokasi & Kapasitas</th>
                    <th>Status Operasional</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $index => $facility)
                    <tr>
                        <td class="ps-4 text-muted">{{ $facilities->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                                    @if($facility->photo)
                                        <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        @if($facility->type === 'ruang_kelas')
                                            <i class="bi bi-easel fs-4 text-primary"></i>
                                        @elseif($facility->type === 'aula')
                                            <i class="bi bi-building fs-4 text-primary"></i>
                                        @elseif($facility->type === 'laboratorium')
                                            <i class="bi bi-cpu fs-4 text-primary"></i>
                                        @elseif($facility->type === 'lapangan')
                                            <i class="bi bi-dribbble fs-4 text-primary"></i>
                                        @else
                                            <i class="bi bi-projector fs-4 text-primary"></i>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $facility->name }}</div>
                                    <small class="badge bg-light text-secondary border font-monospace">{{ $facility->code }}</small>
                                    @if($facility->description)
                                        <small class="text-muted d-block text-truncate" style="max-width: 260px;" title="{{ $facility->description }}">{{ $facility->description }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                {{ $facility->type_label }}
                            </span>
                        </td>
                        <td>
                            <div><i class="bi bi-geo-alt text-danger me-1"></i> {{ $facility->location }}</div>
                            <small class="text-muted"><i class="bi bi-people me-1"></i> {{ $facility->capacity }} Orang</small>
                        </td>
                        <td>
                            @if($facility->status === 'aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                </span>
                            @elseif($facility->status === 'dalam_perbaikan')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-tools me-1"></i> Dalam Perbaikan
                                </span>
                            @elseif($facility->status === 'nonaktif')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-dash-circle me-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('public.schedule', ['facility_id' => $facility->id]) }}" class="btn btn-outline-info btn-sm rounded-pill px-2 py-1" title="Cek Slot Jadwal" target="_blank">
                                    <i class="bi bi-calendar-week"></i>
                                </a>
                                <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" title="Edit Fasilitas">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / menonaktifkan fasilitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" title="Hapus Fasilitas">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-building-slash fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada fasilitas yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($facilities->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $facilities->links() }}
        </div>
    @endif
</div>
@endsection
