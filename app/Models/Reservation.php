<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Reservation (Reservasi Fasilitas)
 *
 * Terkait User Story:
 * - US 1: Menentukan slot waktu ketersediaan tanpa menampilkan data pemohon di kalender publik
 * - US 3: Mengajukan reservasi pada rentang waktu tertentu dengan menyebutkan tujuan
 * - US 4: Membatalkan reservasi sendiri sebelum waktu mulai
 * - US 5: Melihat riwayat dan status reservasi lengkap oleh pengguna
 * - US 8: Antrian reservasi yang menunggu ditinjau oleh Petugas
 * - US 9: Persetujuan/penolakan reservasi manual oleh Petugas dengan pencegahan bentrok jadwal
 * - US 10: Pembatalan darurat oleh Petugas dengan mencantumkan alasan pembatalan
 * - US 17: Rekapitulasi okupansi fasilitas per periode
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * Field atribut reservasi:
     * - reservation_code, user_id, facility_id, reservation_date, start_time, end_time, purpose (US 3)
     * - status: 'menunggu', 'disetujui', 'ditolak', 'dibatalkan_pengguna', 'dibatalkan_petugas' (US 3, 4, 9, 10)
     * - rejection_reason (US 9), cancellation_reason (US 4, 10)
     * - approved_by, approved_at (US 9)
     */
    protected $fillable = [
        'reservation_code',
        'user_id',
        'facility_id',
        'reservation_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'rejection_reason',
        'cancellation_reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pemohon (User)
     * US 5: Detail riwayat pemohon
     * US 8 & US 9: Informasi pemohon untuk petugas
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke fasilitas yang dipesan
     * US 1, US 3, US 5, US 9, US 17
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    /**
     * Relasi ke petugas yang menyetujui/menolak
     * US 5, US 9
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Cek apakah reservasi berstatus menunggu konfirmasi petugas
     * US 8, US 9
     */
    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    /**
     * Cek apakah reservasi telah disetujui
     * US 1, US 9, US 17
     */
    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    /**
     * Cek apakah reservasi ditolak
     * US 9
     */
    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Cek apakah reservasi dibatalkan
     * US 4, US 10
     */
    public function isCancelled(): bool
    {
        return in_array($this->status, ['dibatalkan_pengguna', 'dibatalkan_petugas']);
    }

    /**
     * Validasi apakah reservasi masih dapat dibatalkan mandiri oleh pengguna
     * US 4: Pengguna hanya dapat membatalkan sebelum batas waktu kegiatan dimulai
     */
    public function canBeCancelledByUser(): bool
    {
        if (in_array($this->status, ['ditolak', 'dibatalkan_pengguna', 'dibatalkan_petugas'])) {
            return false;
        }

        // Reservation start datetime
        $startDateTime = Carbon::parse($this->reservation_date->format('Y-m-d').' '.$this->start_time);

        // Allowed if the event hasn't started yet
        return Carbon::now()->lt($startDateTime);
    }

    /**
     * Format rentang waktu slot (e.g. 08:00 - 10:00)
     * US 1, US 3, US 5
     */
    public function getFormattedTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }

    /**
     * Label representasi status reservasi dalam Bahasa Indonesia
     * US 5, US 8, US 9, US 10
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Konfirmasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan_pengguna' => 'Dibatalkan Pengguna',
            'dibatalkan_petugas' => 'Dibatalkan Petugas',
            default => ucfirst($this->status),
        };
    }
}
