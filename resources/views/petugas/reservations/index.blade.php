@extends('layouts.admin')

@section('title', 'Antrian & Persetujuan Reservasi')

@section('page-title', 'Manajemen & Persetujuan Antrean Reservasi Fasilitas')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <p class="text-muted mb-0">
            Tinjau permohonan peminjaman fasilitas kampus oleh mahasiswa, dosen, dan staf. Sistem otomatis memvalidasi jadwal untuk mencegah bentrok pemakaian antar pemohon.
        </p>
    </div>
</div>

<!-- Filter & Pencarian Antrean -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('petugas.reservations.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Cari Pemohon / Kode</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Kode RSV, nama, NIM..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status Reservasi</label>
                <select name="status" class="form-select bg-light">
                    <option value="all">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan_pengguna" {{ request('status') === 'dibatalkan_pengguna' ? 'selected' : '' }}>Dibatalkan Pengguna</option>
                    <option value="dibatalkan_petugas" {{ request('status') === 'dibatalkan_petugas' ? 'selected' : '' }}>Dibatalkan Petugas</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Fasilitas</label>
                <select name="facility_id" class="form-select bg-light">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ request('facility_id') == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Tanggal</label>
                <input type="date" name="date" class="form-control bg-light" value="{{ request('date') }}">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-dark w-100 rounded-3" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['search', 'status', 'facility_id', 'date']))
                    <a href="{{ route('petugas.reservations.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Antrean Reservasi -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Kode & Pemohon</th>
                    <th>Fasilitas</th>
                    <th>Waktu Penggunaan</th>
                    <th>Tujuan & Catatan</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $index => $res)
                    <tr>
                        <td class="ps-4 text-muted">{{ $reservations->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold font-monospace text-primary">{{ $res->reservation_code }}</div>
                            <div class="fw-semibold text-dark">{{ $res->user->name ?? 'User Tidak Diketahui' }}</div>
                            <small class="text-muted">
                                <span class="badge bg-light text-secondary border text-capitalize">{{ $res->user->user_type ?? '-' }}</span>
                                {{ $res->user->identity_number ?? '' }}
                                @if($res->user->phone)
                                    &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $res->user->phone) }}" target="_blank" class="text-success text-decoration-none"><i class="bi bi-whatsapp"></i> {{ $res->user->phone }}</a>
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $res->facility->name ?? 'Fasilitas' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $res->facility->location ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1 text-primary"></i>{{ $res->reservation_date->format('d M Y') }}</div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace">
                                <i class="bi bi-clock me-1"></i>{{ $res->formatted_time }} WIB
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark" style="max-width: 250px;">{{ $res->purpose }}</div>
                            @if($res->rejection_reason)
                                <div class="small text-danger mt-1"><strong>Alasan Tolak:</strong> {{ $res->rejection_reason }}</div>
                            @endif
                            @if($res->cancellation_reason)
                                <div class="small text-warning mt-1"><strong>Alasan Batal:</strong> {{ $res->cancellation_reason }}</div>
                            @endif
                        </td>
                        <td>
                            @if($res->status === 'menunggu')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                </span>
                            @elseif($res->status === 'disetujui')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                </span>
                            @elseif($res->status === 'ditolak')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                </span>
                            @elseif(in_array($res->status, ['dibatalkan_pengguna', 'dibatalkan_petugas']))
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($res->status === 'menunggu')
                                <div class="d-inline-flex gap-1">
                                    <!-- Form Setujui -->
                                    <form action="{{ route('petugas.reservations.approve', $res) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI reservasi {{ $res->reservation_code }} ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                    </form>

                                    <!-- Tombol Tolak (Buka Modal) -->
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $res->id }}">
                                        <i class="bi bi-x-lg me-1"></i> Tolak
                                    </button>
                                </div>

                                <!-- Modal Tolak Reservasi -->
                                <div class="modal fade" id="rejectModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start rounded-4">
                                            <form action="{{ route('petugas.reservations.reject', $res) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Tolak Permohonan Reservasi
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-3">
                                                        Tolak permohonan reservasi <strong>{{ $res->reservation_code }}</strong> untuk pemohon <strong>{{ $res->user->name }}</strong>. Harap cantumkan alasan yang jelas:
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Contoh: Jadwal bertepatan dengan pemeliharaan rutin sarana prasarana..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Tolak Reservasi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @elseif($res->status === 'disetujui')
                                <!-- Tombol Batalkan Darurat (Buka Modal) -->
                                <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#emergencyCancelModal{{ $res->id }}" title="Pembatalan Darurat oleh Petugas">
                                    <i class="bi bi-shield-exclamation me-1"></i> Batal Darurat
                                </button>

                                <!-- Modal Pembatalan Darurat -->
                                <div class="modal fade" id="emergencyCancelModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start rounded-4">
                                            <form action="{{ route('petugas.reservations.emergency_cancel', $res) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-warning text-dark">
                                                        <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Pembatalan Darurat oleh Petugas
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-3">
                                                        Membatalkan reservasi yang sudah disetujui sebelumnya (<strong>{{ $res->reservation_code }}</strong>). Tindakan ini akan mengosongkan kembali slot jadwal fasilitas tersebut.
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Alasan Pembatalan Darurat <span class="text-danger">*</span></label>
                                                        <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Contoh: Terjadi kerusakan mendadak pada instalasi listrik ruang kelas..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4">Batalkan Reservasi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small"><i class="bi bi-check2-all text-muted"></i> Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada antrean reservasi yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reservations->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reservations->links() }}
        </div>
    @endif
</div>
@endsection
