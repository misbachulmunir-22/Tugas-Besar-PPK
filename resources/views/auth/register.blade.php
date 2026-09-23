@extends('layouts.app')

@section('title', 'Daftar Akun Pengguna Baru')

@section('styles')
<style>
    .auth-card {
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
        background: #ffffff;
        overflow: hidden;
    }
    
    .auth-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3c72 50%, #2a5298 100%);
        color: white;
        padding: 2.25rem 2rem 2rem;
        position: relative;
    }

    .form-section-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background-color: #e2e8f0;
    }

    .toggle-password {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 10;
        color: #94a3b8;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: #1e293b;
    }

    .strength-meter-bar {
        height: 4px;
        border-radius: 2px;
        background-color: #e2e8f0;
        transition: all 0.3s ease;
    }

    .role-info-banner {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card auth-card">
                <!-- Header Card -->
                <div class="auth-header text-center">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-white bg-opacity-10 mb-3 shadow-sm">
                        <i class="bi bi-person-plus-fill fs-2 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Registrasi Akun Pengguna</h3>
                    <p class="text-white text-opacity-75 small mb-0">
                        Pendaftaran Mandiri Mahasiswa, Dosen, & Staf — SIMFAS PPK 2026
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">

                    <!-- Banner Informasi Kebijakan Registrasi Sesuai Aturan Proyek -->
                    <div class="role-info-banner mb-4 d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle-fill text-primary fs-4 mt-1 flex-shrink-0"></i>
                        <div class="small text-secondary">
                            <strong class="text-dark d-block mb-1">Ketentuan Pendaftaran Akun:</strong>
                            <ul class="mb-0 ps-3">
                                <li>Formulir ini khusus untuk registrasi mandiri <strong>Pengguna (Mahasiswa / Dosen / Staf)</strong>.</li>
                                <li>Akun <strong>Petugas Fasilitas</strong> dan <strong>Admin</strong> dibuat langsung oleh Administrator (tidak melalui registrasi mandiri).</li>
                                <li>Setelah mendaftar, akun Anda akan berstatus <span class="badge bg-warning text-dark">Pending</span> dan menunggu <strong>verifikasi Administrator</strong> sebelum dapat digunakan untuk login.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Registrasi -->
                    <form action="{{ route('register') }}" method="POST" id="registerForm" novalidate>
                        @csrf

                        <!-- Bagian 1: Data Pribadi & Identitas -->
                        <div class="form-section-title">
                            <i class="bi bi-person-lines-fill text-primary"></i> 1. Informasi Identitas Pengguna
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Nama Lengkap -->
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-semibold small text-secondary">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Contoh: Muhammad Raihan / Dr. Siti Aminah" 
                                           value="{{ old('name') }}" 
                                           required 
                                           maxlength="255">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="name-feedback">Nama lengkap wajib diisi (minimal 3 karakter).</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kategori Pengguna (User Type) -->
                            <div class="col-md-6">
                                <label for="user_type" class="form-label fw-semibold small text-secondary">
                                    Kategori Pengguna <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-mortarboard"></i></span>
                                    <select name="user_type" id="user_type" class="form-select @error('user_type') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('user_type') ? '' : 'selected' }}>-- Pilih Kategori --</option>
                                        <option value="mahasiswa" {{ old('user_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa Aktif</option>
                                        <option value="dosen" {{ old('user_type') == 'dosen' ? 'selected' : '' }}>Dosen / Tenaga Pengajar</option>
                                        <option value="staf" {{ old('user_type') == 'staf' ? 'selected' : '' }}>Tenaga Kependidikan / Staf Kampus</option>
                                        <option value="umum" {{ old('user_type') == 'umum' ? 'selected' : '' }}>Unit Kegiatan / Umum Kampus</option>
                                    </select>
                                    @error('user_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="user-type-feedback">Silakan pilih kategori pengguna Anda.</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nomor Identitas (NIM / NIP) -->
                            <div class="col-md-6">
                                <label for="identity_number" class="form-label fw-semibold small text-secondary">
                                    Nomor Identitas (NIM / NIP) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" 
                                           name="identity_number" 
                                           id="identity_number" 
                                           class="form-control @error('identity_number') is-invalid @enderror" 
                                           placeholder="Contoh: 22/501234/TK/45678" 
                                           value="{{ old('identity_number') }}" 
                                           required 
                                           maxlength="50">
                                    @error('identity_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="identity-feedback">Nomor identitas (NIM/NIP) wajib diisi.</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 2: Kontak & Akses Akun -->
                        <div class="form-section-title">
                            <i class="bi bi-shield-lock text-primary"></i> 2. Akun & Kontak Komunikasi
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Email Kampus -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small text-secondary">
                                    Alamat Email Kampus <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="nama@kampus.ac.id" 
                                           value="{{ old('email') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="email-feedback">Masukkan format email yang valid.</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Gunakan email aktif untuk verifikasi akun dan notifikasi reservasi.</div>
                            </div>

                            <!-- Nomor WhatsApp / Telepon -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold small text-secondary">
                                    Nomor WhatsApp / HP Aktif <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           placeholder="081234567890" 
                                           value="{{ old('phone') }}" 
                                           required 
                                           maxlength="20">
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="phone-feedback">Nomor WhatsApp/HP wajib diisi (minimal 10 digit).</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Untuk konfirmasi darurat fasilitas oleh petugas.</div>
                            </div>

                            <!-- Kata Sandi -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold small text-secondary">
                                    Kata Sandi <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <div class="input-group has-validation">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               class="form-control pe-5 @error('password') is-invalid @enderror" 
                                               placeholder="Minimal 6 karakter" 
                                               required 
                                               autocomplete="new-password">
                                        <span class="toggle-password" onclick="toggleFieldPassword('password', 'passwordToggleIcon')">
                                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="password-feedback">Kata sandi minimal 6 karakter.</div>
                                    @enderror
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted" style="font-size: 0.72rem;">Kekuatan Sandi:</span>
                                        <span id="strengthText" class="fw-semibold" style="font-size: 0.72rem; color: #94a3b8;">Belum diisi</span>
                                    </div>
                                    <div class="strength-meter-bar" id="strengthBar"></div>
                                </div>
                            </div>

                            <!-- Konfirmasi Kata Sandi -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold small text-secondary">
                                    Konfirmasi Kata Sandi <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <div class="input-group has-validation">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-check2-circle"></i></span>
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               class="form-control pe-5" 
                                               placeholder="Ulangi kata sandi" 
                                               required 
                                               autocomplete="new-password">
                                        <span class="toggle-password" onclick="toggleFieldPassword('password_confirmation', 'confirmToggleIcon')">
                                            <i class="bi bi-eye" id="confirmToggleIcon"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback" id="confirm-feedback">Konfirmasi kata sandi tidak cocok.</div>
                                    <div class="valid-feedback" id="confirm-valid-feedback">Kata sandi cocok!</div>
                                </div>
                            </div>
                        </div>

                        <!-- Syarat & Ketentuan -->
                        <div class="mb-4">
                            <div class="form-check p-3 rounded-3 bg-light border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label small text-secondary user-select-none" for="agreeTerms">
                                    Saya menyatakan bahwa data yang diisikan adalah benar dan bersedia mematuhi <strong class="text-dark">Tata Tertib Penggunaan Fasilitas Kampus</strong> serta menunggu proses verifikasi akun oleh Administrator.
                                </label>
                                <div class="invalid-feedback" id="terms-feedback">Anda harus menyetujui pernyataan tata tertib.</div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-gradient py-2 fw-semibold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" id="registerSubmitBtn">
                                <span>Ajukan Pendaftaran Akun</span>
                                <i class="bi bi-send-check-fill"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Link Kembali ke Login -->
                    <div class="text-center pt-3 border-top">
                        <p class="text-muted small mb-0">
                            Sudah memiliki akun terverifikasi? 
                            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                                Masuk ke SIMFAS <i class="bi bi-box-arrow-in-right small"></i>
                            </a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Toggle Show / Hide Password Helper
    function toggleFieldPassword(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (input && icon) {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.classList.toggle('bi-eye', !isPassword);
            icon.classList.toggle('bi-eye-slash', isPassword);
        }
    }

    // 2. Real-time Password Strength Meter
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function () {
        const val = passwordInput.value;
        let strength = 0;

        if (val.length >= 6) strength += 25;
        if (val.length >= 8) strength += 25;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) strength += 25;
        if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) strength += 25;

        strengthBar.style.width = strength + '%';

        if (strength === 0) {
            strengthBar.style.backgroundColor = '#e2e8f0';
            strengthText.innerText = 'Belum diisi';
            strengthText.style.color = '#94a3b8';
        } else if (strength <= 25) {
            strengthBar.style.backgroundColor = '#ef4444';
            strengthText.innerText = 'Sangat Lemah (min 6 karakter)';
            strengthText.style.color = '#ef4444';
        } else if (strength <= 50) {
            strengthBar.style.backgroundColor = '#f59e0b';
            strengthText.innerText = 'Cukup';
            strengthText.style.color = '#f59e0b';
        } else if (strength <= 75) {
            strengthBar.style.backgroundColor = '#3b82f6';
            strengthText.innerText = 'Kuat';
            strengthText.style.color = '#3b82f6';
        } else {
            strengthBar.style.backgroundColor = '#10b981';
            strengthText.innerText = 'Sangat Kuat';
            strengthText.style.color = '#10b981';
        }

        checkPasswordMatch();
    });

    // 3. Real-time Password Confirmation Match
    function checkPasswordMatch() {
        if (!confirmInput.value) {
            confirmInput.classList.remove('is-invalid', 'is-valid');
            return;
        }

        if (passwordInput.value === confirmInput.value) {
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
        } else {
            confirmInput.classList.remove('is-valid');
            confirmInput.classList.add('is-invalid');
        }
    }

    confirmInput.addEventListener('input', checkPasswordMatch);

    // 4. Form Submit Client-side Validation
    const regForm = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const userTypeInput = document.getElementById('user_type');
    const identityInput = document.getElementById('identity_number');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const agreeCheck = document.getElementById('agreeTerms');

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            let isValid = true;

            // Validate Name
            if (!nameInput.value.trim() || nameInput.value.trim().length < 3) {
                nameInput.classList.add('is-invalid');
                isValid = false;
            } else {
                nameInput.classList.remove('is-invalid');
                nameInput.classList.add('is-valid');
            }

            // Validate User Type
            if (!userTypeInput.value) {
                userTypeInput.classList.add('is-invalid');
                isValid = false;
            } else {
                userTypeInput.classList.remove('is-invalid');
                userTypeInput.classList.add('is-valid');
            }

            // Validate Identity Number
            if (!identityInput.value.trim()) {
                identityInput.classList.add('is-invalid');
                isValid = false;
            } else {
                identityInput.classList.remove('is-invalid');
                identityInput.classList.add('is-valid');
            }

            // Validate Email
            if (!emailInput.value.trim() || !validateEmail(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            } else {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
            }

            // Validate Phone
            if (!phoneInput.value.trim() || phoneInput.value.trim().length < 9) {
                phoneInput.classList.add('is-invalid');
                isValid = false;
            } else {
                phoneInput.classList.remove('is-invalid');
                phoneInput.classList.add('is-valid');
            }

            // Validate Password
            if (!passwordInput.value || passwordInput.value.length < 6) {
                passwordInput.classList.add('is-invalid');
                isValid = false;
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
            }

            // Validate Confirmation
            if (passwordInput.value !== confirmInput.value || !confirmInput.value) {
                confirmInput.classList.add('is-invalid');
                isValid = false;
            } else {
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
            }

            // Validate Terms Agreement
            if (!agreeCheck.checked) {
                agreeCheck.classList.add('is-invalid');
                isValid = false;
            } else {
                agreeCheck.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                const submitBtn = document.getElementById('registerSubmitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Mengirim Pendaftaran...';
            }
        });

        // Event listener clear error on input
        [nameInput, userTypeInput, identityInput, emailInput, phoneInput, agreeCheck].forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
            el.addEventListener('change', () => el.classList.remove('is-invalid'));
        });
    }
</script>
@endsection
