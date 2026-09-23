@extends('layouts.admin')

@section('title', 'Manajemen & Verifikasi Pengguna')

@section('page-title', 'Manajemen Pengguna & Verifikasi Pendaftaran')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <p class="text-muted mb-0">
            Kelola data akun pengguna, verifikasi pendaftaran mandiri (Mahasiswa/Dosen/Staf), serta daftarkan akun Petugas secara langsung sesuai ketentuan sistem.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Akun Baru (Langsung)
        </a>
    </div>
</div>

<!-- Nav Tabs Kategori Akun -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-3">
        <ul class="nav nav-pills card-header-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 {{ $tab === 'pending' ? 'active bg-warning text-dark fw-bold' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['tab' => 'pending']) }}">
                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                    <span class="badge {{ $tab === 'pending' ? 'bg-dark text-white' : 'bg-warning text-dark' }} ms-1">{{ $counts['pending'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 {{ $tab === 'pengguna' ? 'active bg-primary' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['tab' => 'pengguna']) }}">
                    <i class="bi bi-people-fill me-1"></i> Pengguna Terverifikasi
                    <span class="badge {{ $tab === 'pengguna' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $counts['pengguna'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 {{ $tab === 'petugas' ? 'active bg-info text-white' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['tab' => 'petugas']) }}">
                    <i class="bi bi-person-badge-fill me-1"></i> Petugas Fasilitas
                    <span class="badge {{ $tab === 'petugas' ? 'bg-white text-info' : 'bg-secondary' }} ms-1">{{ $counts['petugas'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 {{ $tab === 'admin' ? 'active bg-dark text-white' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['tab' => 'admin']) }}">
                    <i class="bi bi-shield-lock-fill me-1"></i> Administrator
                    <span class="badge {{ $tab === 'admin' ? 'bg-white text-dark' : 'bg-secondary' }} ms-1">{{ $counts['admin'] }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card-body p-3 bg-light border-bottom">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, email, atau NIM/NIP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark rounded-3 px-3">Filter</button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.users.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary rounded-3">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Nama & Identitas</th>
                    <th>Email & Kontak</th>
                    <th>Peran / Kategori</th>
                    <th>Status Akun</th>
                    <th>Tanggal Daftar</th>
                    <th class="text-end pe-4">Aksi / Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted"><i class="bi bi-card-text me-1"></i>{{ $user->identity_number ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="bi bi-envelope text-muted me-1"></i> {{ $user->email }}</div>
                            <small class="text-muted"><i class="bi bi-whatsapp text-success me-1"></i> {{ $user->phone ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="badge bg-light text-dark border">{{ ucfirst($user->role) }}</div>
                            <small class="d-block text-muted text-capitalize">{{ $user->user_type }}</small>
                        </td>
                        <td>
                            @if($user->status === 'verified')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                                </span>
                            @elseif($user->status === 'pending')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                </span>
                            @elseif($user->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" title="{{ $user->rejection_reason }}">
                                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="text-end pe-4">
                            @if($user->status === 'pending')
                                <div class="d-inline-flex gap-1">
                                    <!-- Form Setujui / Verifikasi -->
                                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" onsubmit="return confirm('Verifikasi dan aktifkan akun pendaftar ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" title="Setujui dan Verifikasi Akun">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                    </form>

                                    <!-- Tombol Tolak (Buka Modal) -->
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}" title="Tolak Pendaftaran">
                                        <i class="bi bi-x-lg me-1"></i> Tolak
                                    </button>
                                </div>

                                <!-- Modal Tolak Verifikasi -->
                                <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start rounded-4">
                                            <form action="{{ route('admin.users.reject', $user) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Tolak Pendaftaran Akun
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-3">
                                                        Anda akan menolak pendaftaran akun atas nama <strong>{{ $user->name }}</strong> ({{ $user->email }}). Harap berikan alasan penolakan agar pemohon dapat mengetahuinya saat mencoba login:
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Contoh: NIM tidak terdaftar dalam pangkalan data mahasiswa aktif..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Tolak Akun</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small"><i class="bi bi-check2-all text-success"></i> Selesai Diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data pengguna dalam kategori tab ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
