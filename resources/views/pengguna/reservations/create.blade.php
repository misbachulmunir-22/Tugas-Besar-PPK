@extends('layouts.admin')

@section('title', 'Ajukan Reservasi Fasilitas')

@section('page-title', 'Form Pengajuan Reservasi Fasilitas Kampus')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Step 1: Pilih Fasilitas & Tanggal -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">1. Pilih Fasilitas & Tanggal Reservasi</h5>
                        <p class="text-muted small mb-0">
                            Pilih fasilitas yang ingin digunakan serta tentukan tanggal pelaksanaan kegiatan untuk melihat ketersediaan slot 30 menit.
                        </p>
                    </div>
                    <a href="{{ route('pengguna.reservations.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Riwayat Reservasi
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('pengguna.reservations.create') }}" method="GET" class="row g-3 align-items-end" id="facilitySelectorForm">
                    <div class="col-md-6">
                        <label for="facility_id" class="form-label small fw-semibold text-secondary">
                            Pilih Fasilitas Kampus <span class="text-danger">*</span>
                        </label>
                        <select name="facility_id" id="facility_id" class="form-select bg-light rounded-3" onchange="this.form.submit()">
                            <option value="">-- Pilih Fasilitas --</option>
                            @foreach($facilities as $fac)
                                <option value="{{ $fac->id }}" {{ (string)$selectedFacilityId === (string)$fac->id ? 'selected' : '' }}>
                                    {{ $fac->name }} ({{ $fac->type_label }} - {{ $fac->location }} | Kapasitas: {{ $fac->capacity }} Org)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="date" class="form-label small fw-semibold text-secondary">
                            Tanggal Kegiatan <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" id="date" class="form-control bg-light rounded-3" min="{{ date('Y-m-d') }}" value="{{ $selectedDate }}" onchange="this.form.submit()">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-3">
                            <i class="bi bi-search me-1"></i> Cek Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedFacility)
            <!-- Step 2: Tampilan Ketersediaan Slot 30 Menit -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $selectedFacility->name }}</h5>
                            <span class="text-muted small">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $selectedFacility->location }} &bull;
                                <i class="bi bi-people me-1 text-primary"></i>Kapasitas: {{ $selectedFacility->capacity }} orang &bull;
                                <i class="bi bi-calendar-event me-1 text-info"></i>{{ date('d F Y', strtotime($selectedDate)) }}
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Fasilitas Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Keterangan Legend Status Slot -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light mb-4 flex-wrap">
                        <span class="small fw-bold text-dark">Keterangan Slot Waktu:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                            <i class="bi bi-check-lg me-1"></i> Tersedia
                        </span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                            <i class="bi bi-lock-fill me-1"></i> Terpakai / Disetujui
                        </span>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Konfirmasi
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                            <i class="bi bi-cone me-1"></i> Pemeliharaan
                        </span>
                    </div>

                    <!-- Visualisasi Grid Slot 30 Menit -->
                    <div class="row g-2 mb-3">
                        @foreach($slots as $slot)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <div class="p-2 text-center rounded-3 border 
                                    {{ $slot['status'] === 'available' ? 'bg-success bg-opacity-10 border-success text-success' : 
                                       ($slot['status'] === 'booked' ? 'bg-danger bg-opacity-10 border-danger text-danger' : 
                                       ($slot['status'] === 'pending' ? 'bg-warning bg-opacity-10 border-warning text-dark' : 'bg-secondary bg-opacity-10 border-secondary text-muted')) }}">
                                    <div class="fw-bold font-monospace" style="font-size: 0.85rem;">
                                        {{ $slot['start_time'] }} - {{ $slot['end_time'] }}
                                    </div>
                                    <div class="small fw-semibold text-capitalize" style="font-size: 0.7rem;">
                                        @if($slot['status'] === 'available') Tersedia
                                        @elseif($slot['status'] === 'booked') Terpakai
                                        @elseif($slot['status'] === 'pending') Menunggu
                                        @else Pemeliharaan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Step 3: Form Pengajuan Reservasi -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1">2. Lengkapi Detail Pengajuan Pemesanan</h5>
                    <p class="text-muted small mb-0">
                        Tentukan waktu mulai dan selesai (kelipatan slot 30 menit, jam operasional 07:00 - 20:00) serta tujuan penggunaan.
                    </p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('pengguna.reservations.store') }}" method="POST" id="submitReservationForm">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $selectedFacility->id }}">
                        <input type="hidden" name="reservation_date" value="{{ $selectedDate }}">

                        <div class="row g-3 mb-4">
                            <!-- Waktu Mulai -->
                            <div class="col-md-6">
                                <label for="start_time" class="form-label small fw-semibold">
                                    Waktu Mulai <span class="text-danger">*</span>
                                </label>
                                <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('start_time') ? '' : 'selected' }}>-- Pilih Waktu Mulai --</option>
                                    @for($hour = 7; $hour <= 19; $hour++)
                                        @foreach(['00', '30'] as $min)
                                            @php $timeStr = sprintf('%02d:%s', $hour, $min); @endphp
                                            <option value="{{ $timeStr }}" {{ old('start_time') === $timeStr ? 'selected' : '' }}>
                                                {{ $timeStr }} WIB
                                            </option>
                                        @endforeach
                                    @endfor
                                </select>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Selesai -->
                            <div class="col-md-6">
                                <label for="end_time" class="form-label small fw-semibold">
                                    Waktu Selesai <span class="text-danger">*</span>
                                </label>
                                <select name="end_time" id="end_time" class="form-select @error('end_time') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('end_time') ? '' : 'selected' }}>-- Pilih Waktu Selesai --</option>
                                    @for($hour = 7; $hour <= 20; $hour++)
                                        @foreach(['00', '30'] as $min)
                                            @if($hour === 7 && $min === '00') @continue @endif
                                            @if($hour === 20 && $min === '30') @continue @endif
                                            @php $timeStr = sprintf('%02d:%s', $hour, $min); @endphp
                                            <option value="{{ $timeStr }}" {{ old('end_time') === $timeStr ? 'selected' : '' }}>
                                                {{ $timeStr }} WIB
                                            </option>
                                        @endforeach
                                    @endfor
                                </select>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tujuan Penggunaan -->
                            <div class="col-12">
                                <label for="purpose" class="form-label small fw-semibold">
                                    Tujuan Penggunaan & Rencana Kegiatan <span class="text-danger">*</span>
                                </label>
                                <textarea name="purpose" id="purpose" rows="4" class="form-control @error('purpose') is-invalid @enderror" placeholder="Jelaskan secara rinci kegiatan yang akan diadakan, perkiraan jumlah peserta, dan kebutuhan penunjang..." required>{{ old('purpose') }}</textarea>
                                <div class="form-text small text-muted">Minimal 10 karakter. Uraikan kegiatan dengan jelas agar memudahkan peninjauan oleh Petugas.</div>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('pengguna.reservations.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Ajukan Permohonan Reservasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <i class="bi bi-building-gear fs-1 text-primary mb-3"></i>
                <h5 class="fw-bold text-dark">Silakan Pilih Fasilitas Kampus Terlebih Dahulu</h5>
                <p class="text-muted small mb-0">
                    Pilih salah satu fasilitas dari menu dropdown di atas untuk melihat kalender ketersediaan slot waktu dan formulir pemesanan.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
