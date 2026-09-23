@extends('layouts.admin')

@section('title', 'Lapor Kerusakan Fasilitas')

@section('page-title', 'Form Pelaporan Kerusakan Fasilitas Kampus')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">Form Pengaduan & Pelaporan Kerusakan</h5>
                        <p class="text-muted small mb-0">
                            Laporkan kendala, kerusakan fisik, gangguan kelistrikan, atau hilangnya perlengkapan agar segera ditangani oleh tim operasional sarpras kampus.
                        </p>
                    </div>
                    <a href="{{ route('pengguna.reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('pengguna.reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-4">
                        <!-- Fasilitas Terkait -->
                        <div class="col-md-12">
                            <label for="facility_id" class="form-label small fw-semibold">
                                Fasilitas yang Mengalami Kendala / Kerusakan <span class="text-danger">*</span>
                            </label>
                            <select name="facility_id" id="facility_id" class="form-select @error('facility_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('facility_id', $selectedFacilityId) ? '' : 'selected' }}>-- Pilih Fasilitas Kampus --</option>
                                @foreach($facilities as $fac)
                                    <option value="{{ $fac->id }}" {{ (string)old('facility_id', $selectedFacilityId) === (string)$fac->id ? 'selected' : '' }}>
                                        {{ $fac->name }} ({{ $fac->type_label }} - {{ $fac->location }})
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Kerusakan -->
                        <div class="col-md-12">
                            <label for="category" class="form-label small fw-semibold">
                                Kategori Kerusakan <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Kendala --</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Bukti Kerusakan -->
                        <div class="col-md-12">
                            <label for="photo" class="form-label small fw-semibold">
                                Unggah Foto Bukti Kerusakan (Opsional)
                            </label>
                            <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small text-muted">Format: JPG, PNG, WEBP. Maksimal 5 MB. Foto yang jelas akan mempercepat diagnosa teknisi.</div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi Rinci Kerusakan -->
                        <div class="col-12">
                            <label for="description" class="form-label small fw-semibold">
                                Uraian Detail Kerusakan / Masalah <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: AC di sisi utara tidak dingin dan meneteskan air ke meja mahasiswa baris ke-2..." required>{{ old('description') }}</textarea>
                            <div class="form-text small text-muted">Minimal 10 karakter. Uraikan lokasi spesifik di dalam ruangan jika relevan.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('pengguna.reports.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Kirim Laporan Kerusakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
