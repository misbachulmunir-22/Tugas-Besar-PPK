@extends('layouts.app')

@section('title', 'Beranda - Sistem Reservasi & Pelaporan Fasilitas Kampus')

@section('content')
<!-- Hero Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e3c72 100%); border-radius: 0 0 32px 32px; margin-top: -1.5rem;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 text-uppercase fw-semibold" style="letter-spacing: 1px;">
                    <i class="bi bi-stars me-1"></i> Platform Terpadu PPK 2026
                </span>
                <h1 class="display-4 fw-extrabold mb-3">
                    Kelola Fasilitas Kampus Lebih Cepat, Teratur, & Transparan
                </h1>
                <p class="lead text-slate-300 opacity-75 mb-4">
                    Cek ketersediaan ruang kelas, auditorium, laboratorium, dan sarana olahraga secara real-time dengan slot presisi 30 menit, serta laporkan kendala fasilitas secara terpadu.
                </p>

                <!-- Search Card -->
                <div class="card p-3 bg-white text-dark shadow-lg border-0 rounded-4">
                    <form action="{{ route('public.facilities') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control border-0 shadow-none ps-0" placeholder="Cari nama atau kode fasilitas...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select border-0 bg-light rounded-3">
                                <option value="all">Semua Kategori</option>
                                <option value="ruang_kelas">Ruang Kelas</option>
                                <option value="aula">Aula / Auditorium</option>
                                <option value="laboratorium">Laboratorium</option>
                                <option value="lapangan">Lapangan Olahraga</option>
                                <option value="alat">Peralatan / Media</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-gradient w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-search me-1"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 text-center">
                            <i class="bi bi-building-check text-warning fs-1 mb-2 d-block"></i>
                            <h2 class="fw-bold mb-0 text-white">{{ $stats['total_facilities'] }}</h2>
                            <small class="text-white-50">Total Fasilitas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 text-center">
                            <i class="bi bi-check2-circle text-success fs-1 mb-2 d-block"></i>
                            <h2 class="fw-bold mb-0 text-white">{{ $stats['active_facilities'] }}</h2>
                            <small class="text-white-50">Fasilitas Siap Pakai</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 text-center">
                            <i class="bi bi-calendar2-check text-info fs-1 mb-2 d-block"></i>
                            <h2 class="fw-bold mb-0 text-white">{{ $stats['total_reservations'] }}</h2>
                            <small class="text-white-50">Reservasi Berhasil</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-10 text-center">
                            <i class="bi bi-clock-history text-primary-subtle fs-1 mb-2 d-block"></i>
                            <h2 class="fw-bold mb-0 text-white">07-20</h2>
                            <small class="text-white-50">Jam Operasional (WIB)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Action Shortcuts -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom h-100 p-4 text-center border-top border-4 border-primary">
                <div class="rounded-circle bg-primary-subtle text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-calendar3 fs-3"></i>
                </div>
                <h5 class="fw-bold">Cek Ketersediaan Slot 30-Menit</h5>
                <p class="text-muted small">
                    Lihat timeline ketersediaan tanpa perlu login. Slot waktu tetap (07.00 - 20.00 WIB) langsung terpantau hijau (tersedia) atau merah (terisi).
                </p>
                <a href="{{ route('public.schedule') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-auto">
                    Lihat Jadwal Slot <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom h-100 p-4 text-center border-top border-4 border-success">
                <div class="rounded-circle bg-success-subtle text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-send-check fs-3"></i>
                </div>
                <h5 class="fw-bold">Ajukan Reservasi Fasilitas</h5>
                <p class="text-muted small">
                    Mahasiswa, dosen, dan staf kampus dapat memesan fasilitas untuk kegiatan akademik, seminar, riset, atau perlombaan dengan mudah.
                </p>
                @auth
                    <a href="{{ route('pengguna.reservations.create') }}" class="btn btn-outline-success btn-sm rounded-pill mt-auto">
                        Pesan Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm rounded-pill mt-auto">
                        Login untuk Reservasi <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endauth
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom h-100 p-4 text-center border-top border-4 border-danger">
                <div class="rounded-circle bg-danger-subtle text-danger mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold">Pelaporan Kerusakan & Kendala</h5>
                <p class="text-muted small">
                    Temukan AC rusak, proyektor mati, atau kendala fasilitas lainnya? Laporkan dengan foto bukti agar segera diperbaiki petugas.
                </p>
                @auth
                    <a href="{{ route('pengguna.reports.create') }}" class="btn btn-outline-danger btn-sm rounded-pill mt-auto">
                        Buat Laporan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm rounded-pill mt-auto">
                        Login untuk Lapor <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Featured Facilities -->
<div class="container my-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Daftar Fasilitas Kampus Unggulan</h3>
            <p class="text-muted mb-0">Jelajahi sarana prasarana yang siap digunakan untuk berbagai kegiatan.</p>
        </div>
        <a href="{{ route('public.facilities') }}" class="btn btn-light border rounded-pill px-4">
            Lihat Semua <i class="bi bi-chevron-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        @foreach($featuredFacilities as $facility)
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom h-100 overflow-hidden">
                    <div class="position-relative bg-light text-center py-4 border-bottom" style="height: 160px; background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);">
                        @if($facility->type === 'ruang_kelas')
                            <i class="bi bi-easel fs-1 text-primary opacity-75"></i>
                        @elseif($facility->type === 'aula')
                            <i class="bi bi-building fs-1 text-primary opacity-75"></i>
                        @elseif($facility->type === 'laboratorium')
                            <i class="bi bi-cpu fs-1 text-primary opacity-75"></i>
                        @elseif($facility->type === 'lapangan')
                            <i class="bi bi-dribbble fs-1 text-primary opacity-75"></i>
                        @else
                            <i class="bi bi-projector fs-1 text-primary opacity-75"></i>
                        @endif

                        <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 rounded-pill px-3 py-2">
                            {{ $facility->type_label }}
                        </span>

                        <span class="position-absolute top-0 end-0 m-3 badge rounded-pill px-3 py-2 
                            {{ $facility->status === 'aktif' ? 'bg-success' : ($facility->status === 'dalam_perbaikan' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $facility->status)) }}
                        </span>
                    </div>

                    <div class="card-body d-flex flex-direction-column flex-column p-4">
                        <div class="text-muted small mb-1"><i class="bi bi-qr-code me-1"></i> {{ $facility->code }}</div>
                        <h5 class="fw-bold mb-2">{{ $facility->name }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1">
                            {{ Str::limit($facility->description, 95) }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between text-muted small pt-3 border-top mb-3">
                            <div><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $facility->location }}</div>
                            <div><i class="bi bi-people-fill text-primary me-1"></i> Kapasitas {{ $facility->capacity }} Org</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('public.schedule', ['facility_id' => $facility->id]) }}" class="btn btn-light border btn-sm flex-fill rounded-pill">
                                <i class="bi bi-calendar3 me-1"></i> Cek Jadwal
                            </a>
                            @auth
                                <a href="{{ route('pengguna.reservations.create', ['facility_id' => $facility->id]) }}" class="btn btn-primary btn-sm flex-fill rounded-pill">
                                    <i class="bi bi-bookmark-plus me-1"></i> Reservasi
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
