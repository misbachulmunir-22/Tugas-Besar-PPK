<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DamageReport (Laporan Kerusakan & Kendala Fasilitas)
 *
 * Terkait User Story:
 * - US 6: Melaporkan kerusakan/masalah fasilitas (kategori, deskripsi, foto) oleh Pengguna
 * - US 7: Melihat status dan perkembangan laporan oleh Pengguna
 * - US 8: Antrian laporan kerusakan yang menunggu diproses oleh Petugas
 * - US 11: Mengubah status laporan (baru/diproses/selesai/ditolak) dan mengisi catatan resolusi oleh Petugas
 * - US 12: Hubungan penanganan laporan dengan pembaruan status fasilitas 'dalam_perbaikan' / 'aktif'
 * - US 17: Rekapitulasi frekuensi kerusakan per fasilitas/lokasi untuk Admin
 */
class DamageReport extends Model
{
    use HasFactory;

    /**
     * Field atribut laporan kerusakan:
     * - report_code, user_id, facility_id, category, description, photo_path (US 6)
     * - status: 'baru', 'diproses', 'selesai', 'ditolak' (US 7, US 8, US 11)
     * - resolution_notes, handled_by, resolved_at (US 11)
     */
    protected $fillable = [
        'report_code',
        'user_id',
        'facility_id',
        'category',
        'description',
        'photo_path',
        'status',
        'resolution_notes',
        'handled_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pelapor (Pengguna)
     * US 6, US 7, US 8
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke fasilitas yang dilaporkan
     * US 6, US 12, US 17
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    /**
     * Relasi ke petugas yang menangani laporan
     * US 7, US 11
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Label representasi kategori kerusakan dalam Bahasa Indonesia
     * US 6, US 11, US 17
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'fisik_bangunan' => 'Fisik & Bangunan (Pintu, Jendela, Dinding)',
            'kelistrikan_elektronik' => 'Kelistrikan & Elektronik (AC, Lampu, Saklar)',
            'kebersihan' => 'Kebersihan & Sanitasi',
            'alat_rusak_hilang' => 'Peralatan Rusak / Hilang (Proyektor, PC, Mic)',
            'lainnya' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    /**
     * Label representasi status laporan kerusakan dalam Bahasa Indonesia
     * US 7, US 8, US 11
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Baru (Menunggu)',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai / Diperbaiki',
            'ditolak' => 'Ditolak / Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
