<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isCancelled(): bool
    {
        return in_array($this->status, ['dibatalkan_pengguna', 'dibatalkan_petugas']);
    }

    public function canBeCancelledByUser(): bool
    {
        if (in_array($this->status, ['ditolak', 'dibatalkan_pengguna', 'dibatalkan_petugas'])) {
            return false;
        }

        // Reservation start datetime
        $startDateTime = Carbon::parse($this->reservation_date->format('Y-m-d') . ' ' . $this->start_time);
        
        // Allowed if the event hasn't started yet
        return Carbon::now()->lt($startDateTime);
    }

    public function getFormattedTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

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
