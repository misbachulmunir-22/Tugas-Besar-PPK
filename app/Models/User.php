<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 *
 * Terkait User Story:
 * - US 13: Mendaftarkan akun petugas secara langsung oleh Admin
 * - US 14: Mendaftarkan akun pengguna secara langsung oleh Admin
 * - US 15: Verifikasi/penolakan akun pengguna registrasi mandiri sebelum login
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * US 13 & US 14: role (admin, petugas, pengguna), user_type, identity_number
     * US 15: status (pending, verified, rejected), rejection_reason
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type',
        'identity_number',
        'phone',
        'status',
        'rejection_reason',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah role adalah Admin
     * US 13, US 14, US 15, US 16, US 17
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah role adalah Petugas Fasilitas
     * US 8, US 9, US 10, US 11, US 12, US 13
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Cek apakah role adalah Pengguna (Mahasiswa/Dosen/Staf/Umum)
     * US 1, US 2, US 3, US 4, US 5, US 6, US 7, US 14
     */
    public function isPengguna(): bool
    {
        return $this->role === 'pengguna';
    }

    /**
     * Cek apakah status akun telah terverifikasi
     * US 15: Memastikan akun pengguna sudah diverifikasi admin sebelum login
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Cek apakah status akun masih menunggu verifikasi admin
     * US 15: Status pending verifikasi pendaftaran mandiri
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Relasi riwayat reservasi yang diajukan oleh user
     * US 3, US 4, US 5
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relasi laporan kerusakan yang diajukan oleh user
     * US 6, US 7
     */
    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    /**
     * Relasi reservasi yang disetujui/ditolak oleh petugas
     * US 9, US 10
     */
    public function approvedReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'approved_by');
    }

    /**
     * Relasi laporan kerusakan yang ditangani oleh petugas
     * US 11, US 12
     */
    public function handledReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'handled_by');
    }
}
