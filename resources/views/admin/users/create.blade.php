@extends('layouts.admin')

@section('title', 'Tambah Akun Pengguna / Petugas')

@section('page-title', 'Tambah Akun Pengguna / Petugas Baru (Langsung)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1">Form Pendaftaran Akun oleh Admin</h5>
                        <p class="text-muted small mb-0">
                            Akun yang didaftarkan langsung oleh Administrator akan otomatis berstatus <strong>Terverifikasi & Aktif</strong>.
                        </p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.users.store') }}" method="POST" id="adminCreateUserForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <!-- Peran Akun (Role) -->
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold small">
                                Peran Sistem (Role) <span class="text-danger">*</span>
                            </label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas Fasilitas (Staff Operasional)</option>
                                <option value="pengguna" {{ old('role') == 'pengguna' ? 'selected' : '' }}>Pengguna (Mahasiswa/Dosen/Staf)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator Sistem</option>
                            </select>
                            <div class="form-text small text-muted">
                                <em>Catatan: Sesuai aturan sistem, akun <strong>Petugas Fasilitas</strong> hanya dapat dibuat langsung oleh Admin.</em>
                            </div>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Pengguna (User Type) -->
                        <div class="col-md-6">
                            <label for="user_type" class="form-label fw-semibold small">
                                Kategori Identitas <span class="text-danger">*</span>
                            </label>
                            <select name="user_type" id="user_type" class="form-select @error('user_type') is-invalid @enderror" required>
                                <option value="staf" {{ old('user_type') == 'staf' ? 'selected' : '' }}>Staf / Karyawan Kampus</option>
                                <option value="dosen" {{ old('user_type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="mahasiswa" {{ old('user_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="umum" {{ old('user_type') == 'umum' ? 'selected' : '' }}>Umum</option>
                            </select>
                            @error('user_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="col-md-12">
                            <label for="name" class="form-label fw-semibold small">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap beserta gelar (jika ada)" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold small">
                                Alamat Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@kampus.ac.id" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nomor Identitas (NIM / NIP) -->
                        <div class="col-md-6">
                            <label for="identity_number" class="form-label fw-semibold small">
                                Nomor Identitas (NIM / NIP / ID Petugas)
                            </label>
                            <input type="text" name="identity_number" id="identity_number" class="form-control @error('identity_number') is-invalid @enderror" placeholder="Contoh: PTG-2026-001" value="{{ old('identity_number') }}">
                            @error('identity_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nomor Telepon / WA -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold small">
                                Nomor WhatsApp / HP
                            </label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Awal -->
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">
                                Kata Sandi Awal <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan & Daftarkan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
