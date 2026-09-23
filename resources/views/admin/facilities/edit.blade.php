@extends('layouts.admin')

@section('title', 'Edit Fasilitas - ' . $facility->name)

@section('page-title', 'Edit Data Fasilitas Kampus')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">Edit Spesifikasi Fasilitas</h5>
                        <p class="text-muted small mb-0">
                            Perbarui informasi fasilitas <strong>{{ $facility->name }}</strong> ({{ $facility->code }}).
                        </p>
                    </div>
                    <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <!-- Nama Fasilitas -->
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold small">
                                Nama Fasilitas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $facility->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kode Fasilitas -->
                        <div class="col-md-4">
                            <label for="code" class="form-label fw-semibold small">
                                Kode Fasilitas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $facility->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipe / Kategori -->
                        <div class="col-md-4">
                            <label for="type" class="form-label fw-semibold small">
                                Kategori / Tipe <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="ruang_kelas" {{ old('type', $facility->type) === 'ruang_kelas' ? 'selected' : '' }}>Ruang Kelas</option>
                                <option value="aula" {{ old('type', $facility->type) === 'aula' ? 'selected' : '' }}>Aula / Auditorium</option>
                                <option value="laboratorium" {{ old('type', $facility->type) === 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                <option value="alat" {{ old('type', $facility->type) === 'alat' ? 'selected' : '' }}>Peralatan & Media</option>
                                <option value="lapangan" {{ old('type', $facility->type) === 'lapangan' ? 'selected' : '' }}>Lapangan Olahraga</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lokasi Gedung & Lantai -->
                        <div class="col-md-5">
                            <label for="location" class="form-label fw-semibold small">
                                Lokasi Gedung & Lantai <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $facility->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kapasitas Orang -->
                        <div class="col-md-3">
                            <label for="capacity" class="form-label fw-semibold small">
                                Kapasitas (Orang) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $facility->capacity) }}" min="0" required>
                                <span class="input-group-text bg-light">Orang</span>
                            </div>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Operasional -->
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold small">
                                Status Operasional <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="aktif" {{ old('status', $facility->status) === 'aktif' ? 'selected' : '' }}>Aktif / Tersedia untuk Dipesan</option>
                                <option value="dalam_perbaikan" {{ old('status', $facility->status) === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan (Under Maintenance)</option>
                                <option value="nonaktif" {{ old('status', $facility->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Ditutup Sementara)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Fasilitas -->
                        <div class="col-md-6">
                            <label for="photo" class="form-label fw-semibold small">
                                Perbarui Foto Fasilitas (Opsional)
                            </label>
                            <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small text-muted">Biarkan kosong jika tidak ingin mengubah foto.</div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($facility->photo)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                                    <div>
                                        <div class="small fw-bold text-dark">Foto Saat Ini</div>
                                        <small class="text-muted">Foto aktif yang saat ini ditampilkan pada katalog publik.</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Deskripsi Fasilitas -->
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold small">
                                Deskripsi & Sarana Prasarana Pendukung
                            </label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $facility->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.facilities.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
