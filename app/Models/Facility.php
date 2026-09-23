<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

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

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === 'dalam_perbaikan';
    }

    public function isInactive(): bool
    {
        return $this->status === 'nonaktif';
    }

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
