@extends('layouts.admin')

@section('title', 'Manajemen Laporan Kerusakan')

@section('page-title', 'Manajemen & Tindak Lanjut Laporan Kerusakan Fasilitas')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <p class="text-muted mb-0">
            Daftar pelaporan kerusakan fasilitas dari sivitas akademika. Perbarui status penanganan perbaikan dan kelola ketersediaan operasional fasilitas kampus.
        </p>
    </div>
</div>

<!-- Filter & Pencarian Laporan -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 bg-white rounded-4">
        <form method="GET" action="{{ route('petugas.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status Laporan</label>
                <select name="status" class="form-select bg-light">
                    <option value="all">Semua Status</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru (Menunggu Tindak Lanjut)</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Sedang Diproses / Diperbaiki</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Diperbaiki</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Kategori Kerusakan</label>
                <select name="category" class="form-select bg-light">
                    <option value="all">Semua Kategori</option>
                    <option value="fisik_bangunan" {{ request('category') === 'fisik_bangunan' ? 'selected' : '' }}>Fisik & Bangunan</option>
                    <option value="kelistrikan_elektronik" {{ request('category') === 'kelistrikan_elektronik' ? 'selected' : '' }}>Kelistrikan & Elektronik</option>
                    <option value="kebersihan" {{ request('category') === 'kebersihan' ? 'selected' : '' }}>Kebersihan & Sanitasi</option>
                    <option value="alat_rusak_hilang" {{ request('category') === 'alat_rusak_hilang' ? 'selected' : '' }}>Alat Rusak / Hilang</option>
                    <option value="lainnya" {{ request('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Fasilitas Terkait</label>
                <select name="facility_id" class="form-select bg-light">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ request('facility_id') == $fac->id ? 'selected' : '' }}>{{ $fac->name }} ({{ $fac->location }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['status', 'category', 'facility_id']))
                    <a href="{{ route('petugas.reports.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Laporan Kerusakan -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">#</th>
                    <th>Kode & Pelapor</th>
                    <th>Fasilitas</th>
                    <th>Kategori & Deskripsi Masalah</th>
                    <th>Bukti Foto</th>
                    <th>Status Laporan</th>
                    <th class="text-end pe-4">Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $rep)
                    <tr>
                        <td class="ps-4 text-muted">{{ $reports->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold font-monospace text-danger">{{ $rep->report_code }}</div>
                            <div class="fw-semibold text-dark">{{ $rep->user->name ?? 'Pengguna' }}</div>
                            <small class="text-muted">
                                {{ $rep->user->identity_number ?? '-' }} &bull;
                                {{ $rep->created_at ? $rep->created_at->format('d M Y, H:i') : '-' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rep->facility->name ?? 'Fasilitas' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $rep->facility->location ?? '-' }}</small>
                            <div class="mt-1">
                                @if($rep->facility->status === 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem;">
                                        Fasilitas Aktif
                                    </span>
                                @elseif($rep->facility->status === 'dalam_perbaikan')
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill" style="font-size: 0.72rem;">
                                        Dalam Perbaikan
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border mb-1">{{ $rep->category_label }}</span>
                            <p class="small text-muted mb-0 text-break" style="max-width: 280px;">
                                {{ $rep->description }}
                            </p>
                            @if($rep->resolution_notes)
                                <div class="mt-2 p-2 rounded-3 bg-light border small">
                                    <strong class="text-primary d-block"><i class="bi bi-chat-left-text me-1"></i>Catatan Solusi:</strong>
                                    {{ $rep->resolution_notes }}
                                    @if($rep->handler)
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Oleh: {{ $rep->handler->name }}</div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($rep->photo_path)
                                <a href="{{ asset('storage/' . $rep->photo_path) }}" target="_blank" class="d-inline-block position-relative rounded-3 overflow-hidden border shadow-sm" style="width: 54px; height: 54px;">
                                    <img src="{{ asset('storage/' . $rep->photo_path) }}" alt="Bukti Kerusakan" class="w-100 h-100 object-fit-cover">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border"><i class="bi bi-image-fill me-1"></i>Tanpa Foto</span>
                            @endif
                        </td>
                        <td>
                            @if($rep->status === 'baru')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Baru
                                </span>
                            @elseif($rep->status === 'diproses')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-gear-fill me-1"></i> Diproses
                                </span>
                            @elseif($rep->status === 'selesai')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                </span>
                            @elseif($rep->status === 'ditolak')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <!-- Tombol Update Status (Buka Modal) -->
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#updateModal{{ $rep->id }}">
                                <i class="bi bi-tools me-1"></i> Tindak Lanjut
                            </button>

                            <!-- Modal Update Status Laporan -->
                            <div class="modal fade" id="updateModal{{ $rep->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content text-start rounded-4">
                                        <form action="{{ route('petugas.reports.update_status', $rep) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="bi bi-wrench-adjustable text-primary me-2"></i> Update Laporan {{ $rep->report_code }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="p-3 bg-light rounded-3 mb-3">
                                                    <div class="fw-bold">{{ $rep->facility->name }}</div>
                                                    <small class="text-muted">{{ $rep->category_label }} &bull; {{ $rep->description }}</small>
                                                </div>

                                                <!-- Status Penanganan -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Status Penanganan Laporan <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="baru" {{ $rep->status === 'baru' ? 'selected' : '' }}>Baru (Menunggu Penanganan)</option>
                                                        <option value="diproses" {{ $rep->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses / Perbaikan Berjalan</option>
                                                        <option value="selesai" {{ $rep->status === 'selesai' ? 'selected' : '' }}>Selesai / Kerusakan Telah Diperbaiki</option>
                                                        <option value="ditolak" {{ $rep->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Bukan Kerusakan / Tidak Relevan)</option>
                                                    </select>
                                                </div>

                                                <!-- Ubah Status Fasilitas Terkait (User Story 12) -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Sinkronisasi Status Fasilitas Terkait</label>
                                                    <select name="update_facility_status" class="form-select">
                                                        <option value="tidak">Biarkan Status Fasilitas Saat Ini ({{ ucfirst(str_replace('_', ' ', $rep->facility->status)) }})</option>
                                                        <option value="dalam_perbaikan">Ubah Status Fasilitas menjadi "DALAM PERBAIKAN"</option>
                                                        <option value="aktif">Ubah Status Fasilitas menjadi "AKTIF / TERSEDIA"</option>
                                                    </select>
                                                    <div class="form-text small text-muted">
                                                        Jika fasilitas ditandai "Dalam Perbaikan", fasilitas tersebut tidak akan dapat dipesan oleh pengguna di sistem reservasi.
                                                    </div>
                                                </div>

                                                <!-- Catatan Solusi / Perbaikan -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Catatan Solusi / Tindak Lanjut Perbaikan</label>
                                                    <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Uraikan tindakan perbaikan yang telah dilakukan, suku cadang yang diganti, dll...">{{ old('resolution_notes', $rep->resolution_notes) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Pembaruan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                            Tidak ada laporan kerusakan yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
