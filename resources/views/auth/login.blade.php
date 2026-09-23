@extends('layouts.app')

@section('title', 'Portal Autentikasi & Akses SIMFAS 2026')

@section('styles')
<style>
    .auth-card-container {
        max-width: 1050px;
        margin: 0 auto;
    }

    .auth-main-card {
        border-radius: 24px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12);
        background: #ffffff;
        overflow: hidden;
    }

    .auth-hero-side {
        background: linear-gradient(135deg, #0f172a 0%, #1e3c72 55%, #2a5298 100%);
        color: white;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .auth-hero-side::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .auth-tab-btn {
        border: none;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        transition: all 0.25s ease;
        font-size: 0.9rem;
    }

    .auth-tab-btn.active {
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .input-icon-group {
        position: relative;
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

    .demo-pill {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        color: white;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .demo-pill:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        color: white;
    }

    .strength-meter-bar {
        height: 4px;
        border-radius: 2px;
        background-color: #e2e8f0;
        transition: all 0.3s ease;
    }

    .fade-panel {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    .fade-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="container py-3">
    <div class="auth-card-container">
        <div class="card auth-main-card">
            <div class="row g-0">
                
                <!-- Kolom Kiri: Hero & Akun Uji Coba Cepat -->
                <div class="col-lg-5 auth-hero-side">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white border border-white border-opacity-20 mb-3 small fw-semibold">
                            <i class="bi bi-shield-check text-warning"></i> Portal Akses Kampus PPK 2026
                        </div>
                        <h2 class="fw-bold mb-2">SIMFAS 2026</h2>
                        <p class="text-white text-opacity-75 small mb-4">
                            Sistem Terpadu Reservasi & Pelaporan Kerusakan Fasilitas Kampus. Masuk untuk mengelola pinjaman ruangan, alat, lapangan, atau laporkan kendala fasilitas.
                        </p>

                        <!-- Ketentuan Singkat -->
                        <div class="vstack gap-2 mb-4">
                            <div class="d-flex align-items-center gap-2 small text-white text-opacity-90">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Jam Operasional: <strong>07.00 - 20.00</strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 small text-white text-opacity-90">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Durasi Tetap: <strong>Slot 30 Menit</strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 small text-white text-opacity-90">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Registrasi mandiri langsung aktif & siap digunakan.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Akun Demo Cepat (1-Klik Isi Form) -->
                    <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-white text-opacity-90">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Akun Demo Cepat:
                            </span>
                            <span class="badge bg-light bg-opacity-20 text-white" style="font-size: 0.7rem;">Pass: password</span>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="demo-pill" onclick="quickFill('admin@kampus.ac.id', 'password')">
                                    <div class="fw-bold" style="font-size: 0.82rem;"><i class="bi bi-shield-lock"></i> Admin</div>
                                    <small class="text-white text-opacity-75" style="font-size: 0.7rem;">admin@kampus.ac.id</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-pill" onclick="quickFill('petugas@kampus.ac.id', 'password')">
                                    <div class="fw-bold" style="font-size: 0.82rem;"><i class="bi bi-person-badge"></i> Petugas</div>
                                    <small class="text-white text-opacity-75" style="font-size: 0.7rem;">petugas@kampus.ac.id</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-pill" onclick="quickFill('mahasiswa@kampus.ac.id', 'password')">
                                    <div class="fw-bold" style="font-size: 0.82rem;"><i class="bi bi-person"></i> Mahasiswa</div>
                                    <small class="text-white text-opacity-75" style="font-size: 0.7rem;">mahasiswa@kampus.ac.id</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-pill" onclick="quickFill('dosen@kampus.ac.id', 'password')">
                                    <div class="fw-bold" style="font-size: 0.82rem;"><i class="bi bi-mortarboard"></i> Dosen</div>
                                    <small class="text-white text-opacity-75" style="font-size: 0.7rem;">dosen@kampus.ac.id</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Dinamis (Login & Register Mandiri) -->
                <div class="col-lg-7 p-4 p-md-5">

                    <!-- Tab Switcher (Masuk vs Daftar Mandiri) -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <div class="btn-group p-1 bg-light rounded-3" role="group">
                            <button type="button" class="auth-tab-btn active" id="tabLoginBtn" onclick="switchAuthMode('login')">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Akun
                            </button>
                            <button type="button" class="auth-tab-btn" id="tabRegisterBtn" onclick="switchAuthMode('register')">
                                <i class="bi bi-person-plus me-1"></i> Register Mandiri
                            </button>
                        </div>

                        <a href="{{ route('public.facilities') }}" class="small text-muted text-decoration-none">
                            <i class="bi bi-grid me-1"></i> Katalog Fasilitas
                        </a>
                    </div>

                    <!-- Panel 1: FORM LOGIN -->
                    <div class="fade-panel active" id="panelLogin">
                        <div class="mb-4">
                            <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
                            <p class="text-muted small mb-0">Masukkan alamat email dan kata sandi untuk masuk langsung ke Dashboard Anda.</p>
                        </div>

                        <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
                            @csrf

                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="login_email" class="form-label fw-semibold small text-secondary">
                                    Alamat Email Kampus <span class="text-danger">*</span>
                                </label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           id="login_email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="nama@kampus.ac.id" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email" 
                                           autofocus>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="login-email-feedback">Masukkan format email yang valid.</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-3">
                                <label for="login_password" class="form-label fw-semibold small text-secondary">
                                    Kata Sandi <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-group">
                                    <div class="input-group has-validation">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                        <input type="password" 
                                               name="password" 
                                               id="login_password" 
                                               class="form-control pe-5 @error('password') is-invalid @enderror" 
                                               placeholder="••••••••" 
                                               required 
                                               autocomplete="current-password">
                                        <span class="toggle-password" onclick="toggleFieldPassword('login_password', 'loginPassIcon')">
                                            <i class="bi bi-eye" id="loginPassIcon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback" id="login-pass-feedback">Kata sandi wajib diisi.</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remember me -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                                    <label class="form-check-label small text-muted user-select-none" for="remember">
                                        Ingat sesi saya
                                    </label>
                                </div>
                                <span class="small text-muted">Akses Dashboard Langsung</span>
                            </div>

                            <!-- Submit Login Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-gradient py-2 fw-semibold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" id="loginSubmitBtn">
                                    <span>Masuk ke Dashboard</span>
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                            <div class="text-center pt-2">
                                <span class="text-muted small">Belum memiliki akun?</span>
                                <button type="button" class="btn btn-link p-0 small fw-bold text-decoration-none text-primary ms-1" onclick="switchAuthMode('register')">
                                    Daftar Akun Mandiri Sekarang <i class="bi bi-chevron-right small"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Panel 2: FORM REGISTER MANDIRI -->
                    <div class="fade-panel" id="panelRegister">
                        <div class="mb-3">
                            <h4 class="fw-bold text-dark mb-1">Daftar Akun Mandiri</h4>
                            <p class="text-muted small mb-0">Isi data di bawah ini untuk pendaftaran Mahasiswa, Dosen, atau Staf. Akun langsung aktif & siap login.</p>
                        </div>

                        <form action="{{ route('register') }}" method="POST" id="registerForm" novalidate>
                            @csrf

                            <div class="row g-2 mb-3">
                                <!-- Nama Lengkap -->
                                <div class="col-md-12">
                                    <label for="reg_name" class="form-label fw-semibold small text-secondary">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="reg_name" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="Contoh: Muhammad Raihan / Dr. Siti" value="{{ old('name') }}" required>
                                    <div class="invalid-feedback" id="reg-name-feedback">Nama lengkap wajib diisi.</div>
                                </div>

                                <!-- Kategori Pengguna -->
                                <div class="col-md-6">
                                    <label for="reg_user_type" class="form-label fw-semibold small text-secondary">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_type" id="reg_user_type" class="form-select form-select-sm" required>
                                        <option value="mahasiswa" {{ old('user_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa Aktif</option>
                                        <option value="dosen" {{ old('user_type') == 'dosen' ? 'selected' : '' }}>Dosen / Pengajar</option>
                                        <option value="staf" {{ old('user_type') == 'staf' ? 'selected' : '' }}>Staf Kampus</option>
                                        <option value="umum" {{ old('user_type') == 'umum' ? 'selected' : '' }}>Umum Kampus</option>
                                    </select>
                                </div>

                                <!-- NIM / NIP -->
                                <div class="col-md-6">
                                    <label for="reg_identity" class="form-label fw-semibold small text-secondary">
                                        NIM / NIP <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="identity_number" id="reg_identity" class="form-control form-control-sm @error('identity_number') is-invalid @enderror" placeholder="22/501234/TK/45678" value="{{ old('identity_number') }}" required>
                                    <div class="invalid-feedback" id="reg-identity-feedback">NIM/NIP wajib diisi.</div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="reg_email" class="form-label fw-semibold small text-secondary">
                                        Email Kampus <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="reg_email" class="form-control form-control-sm @error('email') is-invalid @enderror" placeholder="email@kampus.ac.id" value="{{ old('email') }}" required>
                                    <div class="invalid-feedback" id="reg-email-feedback">Format email kampus valid.</div>
                                </div>

                                <!-- Telepon / WhatsApp -->
                                <div class="col-md-6">
                                    <label for="reg_phone" class="form-label fw-semibold small text-secondary">
                                        No. WhatsApp/HP <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="phone" id="reg_phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" placeholder="081234567890" value="{{ old('phone') }}" required>
                                    <div class="invalid-feedback" id="reg-phone-feedback">Nomor HP wajib diisi.</div>
                                </div>

                                <!-- Kata Sandi -->
                                <div class="col-md-6">
                                    <label for="reg_password" class="form-label fw-semibold small text-secondary">
                                        Kata Sandi <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" name="password" id="reg_password" class="form-control form-control-sm pe-4 @error('password') is-invalid @enderror" placeholder="Min. 6 karakter" required>
                                        <span class="toggle-password" onclick="toggleFieldPassword('reg_password', 'regPassIcon')">
                                            <i class="bi bi-eye" id="regPassIcon"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback" id="reg-pass-feedback">Minimal 6 karakter.</div>
                                    <div class="strength-meter-bar mt-1" id="strengthBar"></div>
                                </div>

                                <!-- Konfirmasi Sandi -->
                                <div class="col-md-6">
                                    <label for="reg_password_confirmation" class="form-label fw-semibold small text-secondary">
                                        Konfirmasi Sandi <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control form-control-sm pe-4" placeholder="Ulangi sandi" required>
                                        <span class="toggle-password" onclick="toggleFieldPassword('reg_password_confirmation', 'regConfirmIcon')">
                                            <i class="bi bi-eye" id="regConfirmIcon"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback" id="reg-confirm-feedback">Sandi tidak cocok.</div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-gradient py-2 fw-semibold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" id="regSubmitBtn">
                                    <span>Daftarkan Akun & Lanjut Login</span>
                                    <i class="bi bi-send-check"></i>
                                </button>
                            </div>

                            <div class="text-center pt-1">
                                <span class="text-muted small">Sudah memiliki akun?</span>
                                <button type="button" class="btn btn-link p-0 small fw-bold text-decoration-none text-primary ms-1" onclick="switchAuthMode('login')">
                                    Masuk Sekarang <i class="bi bi-chevron-right small"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Switch Mode: Login vs Register
    function switchAuthMode(mode) {
        const tabLogin = document.getElementById('tabLoginBtn');
        const tabRegister = document.getElementById('tabRegisterBtn');
        const panelLogin = document.getElementById('panelLogin');
        const panelRegister = document.getElementById('panelRegister');

        if (mode === 'register') {
            tabLogin.classList.remove('active');
            tabRegister.classList.add('active');
            panelLogin.classList.remove('active');
            panelRegister.classList.add('active');
            document.getElementById('reg_name').focus();
        } else {
            tabRegister.classList.remove('active');
            tabLogin.classList.add('active');
            panelRegister.classList.remove('active');
            panelLogin.classList.add('active');
            document.getElementById('login_email').focus();
        }
    }

    // 2. Toggle Password Visibility
    function toggleFieldPassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input && icon) {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.classList.toggle('bi-eye', !isPassword);
            icon.classList.toggle('bi-eye-slash', isPassword);
        }
    }

    // 3. Quick Fill Demo Credentials
    function quickFill(email, password) {
        switchAuthMode('login');
        const emailEl = document.getElementById('login_email');
        const passEl = document.getElementById('login_password');
        if (emailEl && passEl) {
            emailEl.value = email;
            passEl.value = password;
            emailEl.classList.remove('is-invalid');
            passEl.classList.remove('is-invalid');
            passEl.focus();
        }
    }

    // 4. Client-side Validation for Login
    const loginForm = document.getElementById('loginForm');
    const loginEmail = document.getElementById('login_email');
    const loginPass = document.getElementById('login_password');

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).toLowerCase());
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;

            if (!loginEmail.value.trim() || !isValidEmail(loginEmail.value.trim())) {
                loginEmail.classList.add('is-invalid');
                valid = false;
            } else {
                loginEmail.classList.remove('is-invalid');
            }

            if (!loginPass.value) {
                loginPass.classList.add('is-invalid');
                valid = false;
            } else {
                loginPass.classList.remove('is-invalid');
            }

            if (!valid) {
                e.preventDefault();
            } else {
                const btn = document.getElementById('loginSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses Masuk...';
            }
        });

        loginEmail.addEventListener('input', () => loginEmail.classList.remove('is-invalid'));
        loginPass.addEventListener('input', () => loginPass.classList.remove('is-invalid'));
    }

    // 5. Client-side Validation for Register
    const regForm = document.getElementById('registerForm');
    const regName = document.getElementById('reg_name');
    const regIdentity = document.getElementById('reg_identity');
    const regEmail = document.getElementById('reg_email');
    const regPhone = document.getElementById('reg_phone');
    const regPass = document.getElementById('reg_password');
    const regConfirm = document.getElementById('reg_password_confirmation');
    const strengthBar = document.getElementById('strengthBar');

    if (regPass) {
        regPass.addEventListener('input', function () {
            const val = regPass.value;
            let str = 0;
            if (val.length >= 6) str += 35;
            if (val.length >= 8) str += 25;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) str += 40;

            strengthBar.style.width = str + '%';
            if (str <= 35) strengthBar.style.backgroundColor = '#ef4444';
            else if (str <= 60) strengthBar.style.backgroundColor = '#f59e0b';
            else strengthBar.style.backgroundColor = '#10b981';

            if (regConfirm.value) {
                if (regPass.value === regConfirm.value) {
                    regConfirm.classList.remove('is-invalid');
                    regConfirm.classList.add('is-valid');
                } else {
                    regConfirm.classList.remove('is-valid');
                    regConfirm.classList.add('is-invalid');
                }
            }
        });
    }

    if (regConfirm) {
        regConfirm.addEventListener('input', function () {
            if (regPass.value === regConfirm.value) {
                regConfirm.classList.remove('is-invalid');
                regConfirm.classList.add('is-valid');
            } else {
                regConfirm.classList.remove('is-valid');
                regConfirm.classList.add('is-invalid');
            }
        });
    }

    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            let valid = true;

            if (!regName.value.trim()) { regName.classList.add('is-invalid'); valid = false; }
            if (!regIdentity.value.trim()) { regIdentity.classList.add('is-invalid'); valid = false; }
            if (!regEmail.value.trim() || !isValidEmail(regEmail.value.trim())) { regEmail.classList.add('is-invalid'); valid = false; }
            if (!regPhone.value.trim()) { regPhone.classList.add('is-invalid'); valid = false; }
            if (!regPass.value || regPass.value.length < 6) { regPass.classList.add('is-invalid'); valid = false; }
            if (regPass.value !== regConfirm.value || !regConfirm.value) { regConfirm.classList.add('is-invalid'); valid = false; }

            if (!valid) {
                e.preventDefault();
            } else {
                const btn = document.getElementById('regSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mendaftarkan Akun...';
            }
        });

        [regName, regIdentity, regEmail, regPhone].forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
        });
    }

    // Auto-switch to register if validation errors came from register form
    @if($errors->has('name') || $errors->has('identity_number') || $errors->has('phone'))
        switchAuthMode('register');
    @endif
</script>
@endsection
