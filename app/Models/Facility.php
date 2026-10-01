<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Facility (Fasilitas Kampus)
 *
 * Terkait User Story:
 * - US 1: Menampilkan fasilitas beserta ketersediaan per slot waktu
 * - US 2: Pencarian fasilitas berdasarkan tipe, lokasi, dan kapasitas
 * - US 12: Menandai status fasilitas 'dalam_perbaikan' dan kembali ke 'aktif' oleh Petugas
 * - US 16: Pengelolaan data fasilitas (tambah/edit/nonaktifkan) oleh Admin
 * - US 17: Rekap okupansi dan frekuensi kerusakan fasilitas untuk Admin
 */
class Facility extends Model
{
    use HasFactory;

    /**
     * Field data fasilitas:
     * - name, code, type, location, capacity, description (US 2, US 16)
     * - status: 'aktif', 'dalam_perbaikan', 'nonaktif' (US 1, US 12, US 16)
     */
    protected $fillable = [
        'name',
        'code',
        'type',
        'location',
        'capacity',
        'description',
        'photo',
        'status',
    ];

    /**
     * Relasi reservasi fasilitas
     * US 1, US 3, US 5, US 9, US 17
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relasi laporan kerusakan fasilitas
     * US 6, US 7, US 11, US 12, US 17
     */
    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    /**
     * Cek apakah fasilitas berstatus aktif dan siap dipesan
     * US 1, US 3, US 16
     */
    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Cek apakah fasilitas sedang dalam perbaikan
     * US 12: Ditandai oleh petugas saat menangani kerusakan
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'dalam_perbaikan';
    }

    /**
     * Cek apakah fasilitas dinonaktifkan
     * US 16: Dinonaktifkan oleh Admin
     */
    public function isInactive(): bool
    {
        return $this->status === 'nonaktif';
    }

    /**
     * Label representasi jenis/tipe fasilitas
     * US 2, US 16, US 17
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'ruang_kelas' => 'Ruang Kelas',
            'aula' => 'Aula / Auditorium',
            'laboratorium' => 'Laboratorium',
            'alat' => 'Peralatan & Media',
            'lapangan' => 'Lapangan Olahraga',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
