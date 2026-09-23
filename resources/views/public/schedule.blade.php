@extends('layouts.app')

@section('title', 'Cek Jadwal & Ketersediaan Fasilitas')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold text-dark mb-1">Cek Ketersediaan Jadwal Fasilitas</h3>
                <p class="text-muted small mb-0">
                    Jadwal operasional kampus <strong>07.00 - 20.00</strong> dengan slot waktu tetap berdurasi <strong>30 menit</strong>.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('public.facilities') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-grid me-1"></i> Lihat Katalog Fasilitas
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Fasilitas & Tanggal -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <form action="{{ route('public.schedule') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="facility_id" class="form-label fw-semibold small text-secondary">Pilih Fasilitas Kampus</label>
                <select name="facility_id" id="facility_id" class="form-select rounded-3" onchange="this.form.submit()">
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ $selectedFacilityId == $fac->id ? 'selected' : '' }}>
                            {{ $fac->name }} ({{ ucfirst(str_replace('_', ' ', $fac->type)) }} - {{ $fac->location }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="date" class="form-label fw-semibold small text-secondary">Pilih Tanggal</label>
                <input type="date" name="date" id="date" class="form-control rounded-3" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-3">
                    <i class="bi bi-search me-1"></i> Tampilkan Jadwal
                </button>
            </div>
        </form>
    </div>

    @if($selectedFacility)
        <!-- Detail Fasilitas Terpilih -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1">{{ $selectedFacility->name }}</h4>
                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $selectedFacility->location }} &bull; Kapasitas: {{ $selectedFacility->capacity }} orang</span>
                </div>
                <div>
                    @if($selectedFacility->status === 'aktif')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Status Fasilitas: Aktif
                        </span>
                    @elseif($selectedFacility->status === 'dalam_perbaikan')
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-tools me-1"></i> Sedang Dalam Perbaikan
                        </span>
                    @endif
                </div>
            </div>

            <!-- Petunjuk Legend Status Slot -->
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light mb-4 flex-wrap">
                <span class="small fw-bold text-dark">Keterangan Slot 30 Menit:</span>
                <span class="badge badge-slot-available px-3 py-2 rounded-pill"><i class="bi bi-check-lg me-1"></i> Tersedia</span>
                <span class="badge badge-slot-booked px-3 py-2 rounded-pill"><i class="bi bi-lock-fill me-1"></i> Terpakai / Dipesan</span>
                <span class="badge badge-slot-maintenance px-3 py-2 rounded-pill"><i class="bi bi-cone me-1"></i> Pemeliharaan</span>
            </div>

            <!-- Grid Slot Waktu 30 Menit -->
            <div class="row g-2">
                @forelse($slots as $slot)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="p-3 text-center rounded-3 border 
                            {{ $slot['status'] === 'available' ? 'bg-success bg-opacity-10 border-success text-success' : 
                               ($slot['status'] === 'booked' ? 'bg-danger bg-opacity-10 border-danger text-danger' : 
                               ($slot['status'] === 'pending' ? 'bg-warning bg-opacity-10 border-warning text-dark' : 'bg-secondary bg-opacity-10 border-secondary text-muted')) }}">
                            <div class="fw-bold font-monospace" style="font-size: 0.95rem;">
                                {{ substr($slot['start_time'], 0, 5) }} - {{ substr($slot['end_time'], 0, 5) }}
                            </div>
                            <div class="small fw-semibold mt-1 text-capitalize" style="font-size: 0.75rem;">
                                @if($slot['status'] === 'available') Tersedia
                                @elseif($slot['status'] === 'booked') Terpakai
                                @elseif($slot['status'] === 'pending') Menunggu
                                @else Pemeliharaan
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        Tidak ada slot waktu yang tersedia pada tanggal ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                @auth
                    @if(auth()->user()->isPengguna())
                        <a href="{{ route('pengguna.reservations.create', ['facility_id' => $selectedFacility->id, 'date' => $selectedDate]) }}" class="btn btn-gradient rounded-pill px-4 shadow-sm">
                            <i class="bi bi-calendar-plus me-1"></i> Ajukan Reservasi Fasilitas Ini
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-gradient rounded-pill px-4 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk untuk Mengajukan Reservasi
                    </a>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
