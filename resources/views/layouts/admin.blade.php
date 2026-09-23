<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIMFAS 2026</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 270px;
            background-color: var(--sidebar-bg);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.5rem;
            color: #fff;
            font-weight: 800;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 1.25rem 0.75rem;
            flex-grow: 1;
        }

        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 0.75rem 0.75rem 0.25rem;
            font-weight: 700;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-link.active {
            background-color: var(--sidebar-active);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .sidebar-link i {
            font-size: 1.15rem;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .top-navbar {
            background: #ffffff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-body {
            padding: 2rem;
            flex-grow: 1;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.2);
        }

        .btn-gradient:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                left: -270px;
                top: 0;
                bottom: 0;
            }
            .sidebar.show {
                left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="dashboardSidebar">
            <a href="{{ route('home') }}" class="sidebar-brand">
                <i class="bi bi-building-fill-gear text-primary fs-3"></i>
                <div>
                    <div>SIMFAS 2026</div>
                    <small class="text-secondary fw-normal fs-6">Sistem Fasilitas</small>
                </div>
            </a>

            <div class="sidebar-nav">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <!-- Admin Menu -->
                    <div class="nav-section-title">ADMINISTRASI UTAMA</div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                    </a>
                    <a href="{{ route('admin.facilities.index') }}" class="sidebar-link {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Data Fasilitas
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Manajemen Akun
                    </a>
                    <a href="{{ route('admin.rekap.index') }}" class="sidebar-link {{ request()->routeIs('admin.rekap.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Rekap & Ekspor
                    </a>

                @elseif(auth()->check() && auth()->user()->isPetugas())
                    <!-- Petugas Menu -->
                    <div class="nav-section-title">PETUGAS OPERASIONAL</div>
                    <a href="{{ route('petugas.dashboard') }}" class="sidebar-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-kanban"></i> Dashboard Petugas
                    </a>
                    <a href="{{ route('petugas.reservations.index') }}" class="sidebar-link {{ request()->routeIs('petugas.reservations.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Antrian Reservasi
                    </a>
                    <a href="{{ route('petugas.reports.index') }}" class="sidebar-link {{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i> Laporan Kerusakan
                    </a>

                @else
                    <!-- Pengguna Menu -->
                    <div class="nav-section-title">PORTAL PENGGUNA</div>
                    <a href="{{ route('pengguna.dashboard') }}" class="sidebar-link {{ request()->routeIs('pengguna.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Dashboard Saya
                    </a>
                    <a href="{{ route('pengguna.reservations.create') }}" class="sidebar-link {{ request()->routeIs('pengguna.reservations.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Ajukan Reservasi
                    </a>
                    <a href="{{ route('pengguna.reservations.index') }}" class="sidebar-link {{ request()->routeIs('pengguna.reservations.index') || request()->routeIs('pengguna.reservations.show') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Riwayat Reservasi
                    </a>
                    <a href="{{ route('pengguna.reports.create') }}" class="sidebar-link {{ request()->routeIs('pengguna.reports.create') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-diamond"></i> Lapor Kerusakan
                    </a>
                    <a href="{{ route('pengguna.reports.index') }}" class="sidebar-link {{ request()->routeIs('pengguna.reports.index') || request()->routeIs('pengguna.reports.show') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Status Laporan
                    </a>
                @endif

                <div class="nav-section-title mt-3">NAVIGASI UMUM</div>
                <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                    <i class="bi bi-globe"></i> Halaman Publik <i class="bi bi-box-arrow-up-right ms-auto fs-6"></i>
                </a>
                <a href="{{ route('public.schedule') }}" class="sidebar-link" target="_blank">
                    <i class="bi bi-calendar-week"></i> Cek Jadwal Slot <i class="bi bi-box-arrow-up-right ms-auto fs-6"></i>
                </a>
            </div>

            <!-- Profile Info Footer -->
            <div class="p-3 border-top border-secondary border-opacity-25 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="text-white small fw-bold text-truncate">{{ auth()->user()?->name ?? 'Pengguna' }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">Role: <span class="text-info text-capitalize">{{ auth()->user()?->role ?? 'Guest' }}</span></div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm text-danger p-0 border-0" title="Keluar">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none border" id="sidebarToggleBtn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-person-badge me-1"></i> {{ ucfirst(auth()->user()->role) }} ({{ ucfirst(auth()->user()->user_type) }})
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Alerts -->
            <div class="container-fluid px-4 pt-3">
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
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Terjadi kesalahan validasi:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('dashboardSidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
