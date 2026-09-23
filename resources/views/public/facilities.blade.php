@extends('layouts.app')

@section('title', 'Katalog Fasilitas Kampus - SIMFAS 2026')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold">Katalog Fasilitas Kampus</h2>
        <p class="text-muted">Cari dan temukan fasilitas yang sesuai dengan kebutuhan kegiatan Anda.</p>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom p-4 mb-4">
        <form action="{{ route('public.facilities') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Pencarian Kata Kunci</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="Nama, kode, atau deskripsi fasilitas...">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Kategori Fasilitas</label>
                <select name="type" class="form-select bg-light">
                    <option value="all">Semua Kategori</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Lokasi Gedung</label>
                <select name="location" class="form-select bg-light">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Min. Kapasitas</label>
                <input type="number" name="min_capacity" value="{{ request('min_capacity') }}" class="form-control bg-light" placeholder="Contoh: 30" min="1">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                <a href="{{ route('public.facilities') }}" class="btn btn-light border px-4 rounded-pill">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-funnel me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Facilities Grid -->
    <div class="row g-4">
        @forelse($facilities as $facility)
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom h-100 overflow-hidden">
                    <div class="position-relative bg-light text-center py-4 border-bottom" style="height: 160px; background: linear-gradient(135deg, #e2e8f0 0%, #edf2f7 100%);">
                        @if($facility->photo)
                            <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100">
                                @if($facility->type === 'ruang_kelas')
                                    <i class="bi bi-easel fs-1 text-primary opacity-50"></i>
                                @elseif($facility->type === 'aula')
                                    <i class="bi bi-building fs-1 text-primary opacity-50"></i>
                                @elseif($facility->type === 'laboratorium')
                                    <i class="bi bi-cpu fs-1 text-primary opacity-50"></i>
                                @elseif($facility->type === 'lapangan')
                                    <i class="bi bi-dribbble fs-1 text-primary opacity-50"></i>
                                @else
                                    <i class="bi bi-projector fs-1 text-primary opacity-50"></i>
                                @endif
                            </div>
                        @endif

                        <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 rounded-pill px-3 py-2">
                            {{ $facility->type_label }}
                        </span>

                        <span class="position-absolute top-0 end-0 m-3 badge rounded-pill px-3 py-2 
                            {{ $facility->status === 'aktif' ? 'bg-success' : ($facility->status === 'dalam_perbaikan' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $facility->status)) }}
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <div class="text-muted small mb-1"><i class="bi bi-qr-code me-1"></i> {{ $facility->code }}</div>
                        <h5 class="fw-bold mb-2">{{ $facility->name }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1">
                            {{ $facility->description ?? 'Tidak ada keterangan tambahan.' }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between text-muted small pt-3 border-top mb-3">
                            <div><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $facility->location }}</div>
                            <div><i class="bi bi-people-fill text-primary me-1"></i> {{ $facility->capacity }} Orang</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('public.schedule', ['facility_id' => $facility->id]) }}" class="btn btn-light border btn-sm flex-fill rounded-pill">
                                <i class="bi bi-calendar-week me-1"></i> Cek Slot
                            </a>
                            @auth
                                @if(auth()->user()->isPengguna() && $facility->status === 'aktif')
                                    <a href="{{ route('pengguna.reservations.create', ['facility_id' => $facility->id]) }}" class="btn btn-primary btn-sm flex-fill rounded-pill">
                                        <i class="bi bi-bookmark-plus me-1"></i> Pesan
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm flex-fill rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-secondary">Tidak ada fasilitas yang sesuai dengan filter pencarian.</h5>
                <p class="text-muted">Coba ubah kata kunci atau reset filter pencarian Anda.</p>
                <a href="{{ route('public.facilities') }}" class="btn btn-outline-primary rounded-pill px-4">Reset Filter</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $facilities->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
