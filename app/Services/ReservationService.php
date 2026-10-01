<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Layanan ReservationService
 *
 * Terkait User Story:
 * - US 1: Menghitung ketersediaan per slot waktu 30 menit tanpa mengekspos identitas pemohon ke publik
 * - US 3: Validasi aturan rentang waktu slot 30 menit dan jam operasional kampus 07:00 - 20:00
 * - US 9: Deteksi dan pencegahan bentrok jadwal reservasi otomatis
 */
class ReservationService
{
    /**
     * Validasi aturan waktu 30 menit dan jam operasional 07:00 - 20:00
     * US 3: Pengguna mengajukan reservasi pada rentang waktu operasional yang valid
     */
    public function validateTimeSlot(string $startTime, string $endTime): void
    {
        // Normalisasi format waktu ke H:i (e.g. 07:00)
        $start = Carbon::createFromFormat('H:i', substr($startTime, 0, 5));
        $end = Carbon::createFromFormat('H:i', substr($endTime, 0, 5));

        $opStart = Carbon::createFromFormat('H:i', '07:00');
        $opEnd = Carbon::createFromFormat('H:i', '20:00');

        if ($start->lt($opStart) || $end->gt($opEnd)) {
            throw ValidationException::withMessages([
                'start_time' => 'Waktu reservasi wajib berada dalam jam operasional kampus (07:00 - 20:00 WIB).',
            ]);
        }

        if ($start->gte($end)) {
            throw ValidationException::withMessages([
                'end_time' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            ]);
        }

        // Cek kelipatan 30 menit (menit harus 00 atau 30)
        $startMinute = (int) $start->format('i');
        $endMinute = (int) $end->format('i');

        if (! in_array($startMinute, [0, 30]) || ! in_array($endMinute, [0, 30])) {
            throw ValidationException::withMessages([
                'start_time' => 'Waktu mulai dan selesai wajib merupakan kelipatan slot 30 menit (:00 atau :30).',
            ]);
        }

        // Durasi minimal 30 menit
        if ($start->diffInMinutes($end) < 30) {
            throw ValidationException::withMessages([
                'end_time' => 'Durasi pemesanan minimal adalah 1 slot (30 menit).',
            ]);
        }
    }

    /**
     * Cek apakah terjadi bentrok jadwal dengan reservasi yang sudah disetujui
     * US 9: Mencegah persetujuan reservasi yang bentrok jadwal pada fasilitas yang sama
     * US 3: Pengecekan saat pengajuan reservasi oleh pengguna
     */
    public function hasConflict(int $facilityId, string $date, string $startTime, string $endTime, ?int $excludeReservationId = null): bool
    {
        $start = substr($startTime, 0, 5);
        $end = substr($endTime, 0, 5);

        $query = Reservation::where('facility_id', $facilityId)
            ->whereDate('reservation_date', $date)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->exists();
    }

    /**
     * Mendapatkan daftar slot 30 menit beserta status ketersediaannya (untuk grid/kalender publik)
     * US 1: Menampilkan status ketersediaan tanpa menampilkan data pemohon atau tujuan ke publik
     */
    public function getDailySlots(Facility $facility, string $date): array
    {
        $slots = [];
        $current = Carbon::createFromFormat('H:i', '07:00');
        $end = Carbon::createFromFormat('H:i', '20:00');

        // Ambil semua reservasi disetujui pada tanggal tersebut
        $approvedReservations = Reservation::where('facility_id', $facility->id)
            ->whereDate('reservation_date', $date)
            ->where('status', 'disetujui')
            ->get();

        // Ambil semua reservasi pending untuk info internal
        $pendingReservations = Reservation::where('facility_id', $facility->id)
            ->whereDate('reservation_date', $date)
            ->where('status', 'menunggu')
            ->get();

        while ($current->lt($end)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes(30);

            $slotStartStr = $slotStart->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');

            $isBooked = false;
            foreach ($approvedReservations as $res) {
                $resStart = substr($res->start_time, 0, 5);
                $resEnd = substr($res->end_time, 0, 5);

                if ($slotStartStr < $resEnd && $slotEndStr > $resStart) {
                    $isBooked = true;
                    break;
                }
            }

            $isPending = false;
            if (! $isBooked) {
                foreach ($pendingReservations as $res) {
                    $resStart = substr($res->start_time, 0, 5);
                    $resEnd = substr($res->end_time, 0, 5);

                    if ($slotStartStr < $resEnd && $slotEndStr > $resStart) {
                        $isPending = true;
                        break;
                    }
                }
            }

            $status = 'available';
            if ($facility->status === 'dalam_perbaikan') {
                $status = 'maintenance';
            } elseif ($facility->status === 'nonaktif') {
                $status = 'inactive';
            } elseif ($isBooked) {
                $status = 'booked';
            } elseif ($isPending) {
                $status = 'pending';
            }

            // US 1: Hanya data slot waktu dan status publik (tanpa nama pemohon/tujuan)
            $slots[] = [
                'time_range' => $slotStartStr.' - '.$slotEndStr,
                'start_time' => $slotStartStr,
                'end_time' => $slotEndStr,
                'status' => $status,
                'is_available' => ($status === 'available'),
            ];

            $current->addMinutes(30);
        }

        return $slots;
    }
}
