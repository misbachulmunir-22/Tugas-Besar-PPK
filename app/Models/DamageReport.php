<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReport extends Model
{
    use HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

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
