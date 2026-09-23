<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Reservasi & Pelaporan Fasilitas Kampus') - SIMFAS 2026</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --accent-gradient: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.06);
            --card-shadow-hover: 0 15px 35px rgba(0,0,0,0.12);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            padding: 0.85rem 0;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.35rem;
            background: linear-gradient(135deg, #1e3c72, #0d6efd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            font-weight: 500;
            color: #475569;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #0d6efd !important;
            background-color: #eff6ff;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            box-shadow: 0 4px 15px rgba(30, 60, 114, 0.25);
            transition: all 0.2s ease;
        }

        .btn-gradient:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.35);
        }

        .btn-accent {
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.25);
        }

        .btn-accent:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.35);
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .badge-slot-available {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-slot-booked {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-slot-maintenance {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .footer-custom {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 2rem 0;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand brand-logo" href="{{ route('home') }}">
                <i class="bi bi-building-fill-gear text-primary"></i>
                <span>SIMFAS 2026</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-sidebar bi bi-list fs-2"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.facilities') ? 'active' : '' }}" href="{{ route('public.facilities') }}">
                            <i class="bi bi-grid me-1"></i> Katalog Fasilitas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.schedule') ? 'active' : '' }}" href="{{ route('public.schedule') }}">
                            <i class="bi bi-calendar-week me-1"></i> Cek Jadwal Slot
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-speedometer2 me-1"></i> Panel Admin
                            </a>
                        @elseif(auth()->user()->isPetugas())
                            <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-kanban me-1"></i> Dashboard Petugas
                            </a>
                        @else
                            <a href="{{ route('pengguna.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-person-circle me-1"></i> Dashboard Saya
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm rounded-pill px-3 text-danger border">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm rounded-pill px-3 border">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-gradient btn-sm rounded-pill px-3">
                            <i class="bi bi-person-plus me-1"></i> Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages Container -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-hourglass-split fs-5 me-2 text-warning"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Terjadi beberapa kesalahan pengisian form:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container text-center">
            <div class="brand-logo justify-content-center mb-2">
                <i class="bi bi-building-fill-gear text-primary"></i>
                <span>SIMFAS KAMPUS 2026</span>
            </div>
            <p class="text-muted small mb-0">
                Sistem Reservasi & Pelaporan Fasilitas Kampus Terpadu — Platform PPK 2026.
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
