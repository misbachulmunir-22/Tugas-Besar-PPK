@extends('layouts.admin')

@section('title', 'Detail Reservasi - ' . $reservation->reservation_code)

@section('page-title', 'Detail Permohonan Reservasi Fasilitas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Kartu Utama Detail Reservasi -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Status -->
            <div class="card-header p-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="text-muted small">KODE BOOKING</span>
                    <h4 class="fw-bold font-monospace text-primary mb-0">{{ $reservation->reservation_code }}</h4>
                </div>
                <div>
                    @if($reservation->status === 'menunggu')
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan
                        </span>
                    @elseif($reservation->status === 'disetujui')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui & Terjadwal
                        </span>
                    @elseif($reservation->status === 'ditolak')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                        </span>
                    @elseif(in_array($reservation->status, ['dibatalkan_pengguna', 'dibatalkan_petugas']))
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- Body Detail -->
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Informasi Fasilitas -->
                    <div class="col-md-6">
                        <label class="small text-muted fw-semibold d-block mb-1">FASILITAS KAMPUS</label>
                        <h5 class="fw-bold text-dark mb-1">{{ $reservation->facility->name }}</h5>
                        <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $reservation->facility->location }}</div>
                        <span class="badge bg-light text-dark border">{{ $reservation->facility->type_label }}</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>Kapasitas: {{ $reservation->facility->capacity }} Orang</span>
                    </div>

                    <!-- Waktu Reservasi -->
                    <div class="col-md-6">
                        <label class="small text-muted fw-semibold d-block mb-1">JADWAL PEMAKAIAN</label>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-calendar-event fs-4 text-primary"></i>
                            <div>
                                <strong class="d-block text-dark">{{ $reservation->reservation_date->format('l, d F Y') }}</strong>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-monospace">
                                    <i class="bi bi-clock me-1"></i>{{ $reservation->formatted_time }} WIB
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-0"></div>

                    <!-- Tujuan Penggunaan -->
                    <div class="col-12">
                        <label class="small text-muted fw-semibold d-block mb-2">TUJUAN & DESKRIPSI KEGIATAN</label>
                        <div class="p-3 bg-light rounded-3 text-dark">
                            {{ $reservation->purpose }}
                        </div>
                    </div>

                    <!-- Informasi Tambahan (Approval / Rejection) -->
                    @if($reservation->status === 'disetujui' && $reservation->approver)
                        <div class="col-12">
                            <div class="alert alert-success rounded-3 mb-0 d-flex align-items-center gap-3">
                                <i class="bi bi-patch-check-fill fs-3 text-success"></i>
                                <div>
                                    <strong>Disetujui oleh Petugas:</strong> {{ $reservation->approver->name }}
                                    <div class="small text-muted">Pada {{ $reservation->approved_at ? $reservation->approved_at->format('d M Y, H:i') : '-' }} WIB</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($reservation->rejection_reason)
                        <div class="col-12">
                            <div class="alert alert-danger rounded-3 mb-0">
                                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan Penolakan oleh Petugas:</strong>
                                <p class="mb-0 mt-1 small">{{ $reservation->rejection_reason }}</p>
                            </div>
                        </div>
                    @endif

                    @if($reservation->cancellation_reason)
                        <div class="col-12">
                            <div class="alert alert-warning rounded-3 mb-0">
                                <strong><i class="bi bi-info-circle-fill me-1"></i> Alasan Pembatalan:</strong>
                                <p class="mb-0 mt-1 small">{{ $reservation->cancellation_reason }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Aksi -->
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('pengguna.reservations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                </a>

                <div class="d-flex gap-2">
                    @if($reservation->canBeCancelledByUser())
                        <!-- Tombol Buka Modal Batalkan -->
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Reservasi
                        </button>

                        <!-- Modal Batalkan Reservasi -->
                        <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content text-start rounded-4">
                                    <form action="{{ route('pengguna.reservations.cancel', $reservation) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold text-danger">
                                                <i class="bi bi-exclamation-octagon-fill me-1"></i> Batalkan Reservasi
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">
                                                Apakah Anda yakin ingin membatalkan permohonan reservasi <strong>{{ $reservation->reservation_code }}</strong>? Tindakan ini tidak dapat diurungkan.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Alasan Pembatalan (Opsional)</label>
                                                <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Agenda rapat dimajukan / batal dilaksanakan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Batalkan Reservasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
