<?php

namespace Database\Seeders;

use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Default untuk Setiap Role & Skenario
        $admin = User::create([
            'name' => 'Administrator Kampus',
            'email' => 'admin@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'user_type' => 'staf',
            'identity_number' => 'ADM-2026-001',
            'phone' => '081234567890',
            'status' => 'verified',
            'email_verified_at' => now(),
        ]);

        $petugas = User::create([
            'name' => 'Bambang Sudarsono (Petugas Fasilitas)',
            'email' => 'petugas@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'user_type' => 'staf',
            'identity_number' => 'PTG-2026-042',
            'phone' => '081298765432',
            'status' => 'verified',
            'email_verified_at' => now(),
        ]);

        $mahasiswa = User::create([
            'name' => 'Budi Santoso',
            'email' => 'mahasiswa@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengguna',
            'user_type' => 'mahasiswa',
            'identity_number' => '22/501234/TK/45678',
            'phone' => '085712345678',
            'status' => 'verified',
            'email_verified_at' => now(),
        ]);

        $dosen = User::create([
            'name' => 'Dr. Ir. Hendra Wijaya, M.T.',
            'email' => 'dosen@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengguna',
            'user_type' => 'dosen',
            'identity_number' => '198203152010121002',
            'phone' => '081387654321',
            'status' => 'verified',
            'email_verified_at' => now(),
        ]);

        $pendaftarPending = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'pendaftar@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengguna',
            'user_type' => 'mahasiswa',
            'identity_number' => '24/512345/TK/56789',
            'phone' => '082133445566',
            'status' => 'pending',
        ]);

        // 2. Data Master Fasilitas Kampus
        $fac1 = Facility::create([
            'name' => 'Ruang Kelas 101 - Multimedia Theory',
            'code' => 'RK-101',
            'type' => 'ruang_kelas',
            'location' => 'Gedung A, Lantai 1',
            'capacity' => 45,
            'description' => 'Ruang kelas ber-AC dilengkapi proyektor HD, whiteboard magnetik, podium dosen, dan sound system.',
            'status' => 'aktif',
        ]);

        $fac2 = Facility::create([
            'name' => 'Smart Classroom 204',
            'code' => 'RK-204',
            'type' => 'ruang_kelas',
            'location' => 'Gedung A, Lantai 2',
            'capacity' => 50,
            'description' => 'Ruang kelas interaktif dengan smart interactive board, kamera auto-tracking untuk hybrid class, dan meja modular.',
            'status' => 'aktif',
        ]);

        $fac3 = Facility::create([
            'name' => 'Auditorium Graha Nusantara',
            'code' => 'AULA-01',
            'type' => 'aula',
            'location' => 'Gedung Rektorat, Lantai 3',
            'capacity' => 450,
            'description' => 'Aula besar untuk seminar nasional, kuliah umum, pertunjukan seni, dan wisuda. Dilengkapi panggung megah dan sound theater.',
            'status' => 'aktif',
        ]);

        $fac4 = Facility::create([
            'name' => 'Laboratorium Cyber Security & AI',
            'code' => 'LAB-CS01',
            'type' => 'laboratorium',
            'location' => 'Gedung B, Lantai 2',
            'capacity' => 35,
            'description' => '35 unit PC Workstation high-end dengan GPU RTX, dual monitor, dedicated gigabit network, dan smart projector.',
            'status' => 'aktif',
        ]);

        $fac5 = Facility::create([
            'name' => 'Laboratorium Hardware & IoT',
            'code' => 'LAB-IOT',
            'type' => 'laboratorium',
            'location' => 'Gedung B, Lantai 3',
            'capacity' => 30,
            'description' => 'Meja kerja solder, oscilloscope, mikrokontroler kit ESP32/Raspberry Pi, sensor station, dan 3D printer.',
            'status' => 'dalam_perbaikan',
        ]);

        $fac6 = Facility::create([
            'name' => 'Lapangan Futsal Sport Center',
            'code' => 'LPG-FUTSAL',
            'type' => 'lapangan',
            'location' => 'Kompleks Olahraga Barat',
            'capacity' => 20,
            'description' => 'Lapangan futsal vinyl standar kompetisi dengan penerangan lampu sorot LED malam hari dan tribun penonton.',
            'status' => 'aktif',
        ]);

        $fac7 = Facility::create([
            'name' => 'Lapangan Basket Hall Utama',
            'code' => 'LPG-BASKET',
            'type' => 'lapangan',
            'location' => 'Kompleks Olahraga Timur',
            'capacity' => 30,
            'description' => 'Lapangan basket indoor lantai kayu parket, ring hidrolik, dan scoreboard digital.',
            'status' => 'aktif',
        ]);

        $fac8 = Facility::create([
            'name' => 'Set Proyektor Laser 4K & Sound Portabel',
            'code' => 'ALT-PRJ01',
            'type' => 'alat',
            'location' => 'Ruang Logistik & Sarpras Gedung C',
            'capacity' => 1,
            'description' => 'Proyektor Ultra Bright 6000 Lumens portabel beserta wireless microphone dan portable active speaker.',
            'status' => 'aktif',
        ]);

        // 3. Sample Data Reservasi
        $today = Carbon::today()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Reservasi 1: Disetujui (Mahasiswa - Hari ini 08:00 - 10:00)
        Reservation::create([
            'reservation_code' => 'RSV-20260914-001',
            'user_id' => $mahasiswa->id,
            'facility_id' => $fac1->id,
            'reservation_date' => $today,
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Latihan dan simulasi presentasi tugas besar mata kuliah PPK kelompok 4.',
            'status' => 'disetujui',
            'approved_by' => $petugas->id,
            'approved_at' => now()->subHours(2),
        ]);

        // Reservasi 2: Disetujui (Dosen - Hari ini 13:00 - 15:30)
        Reservation::create([
            'reservation_code' => 'RSV-20260914-002',
            'user_id' => $dosen->id,
            'facility_id' => $fac4->id,
            'reservation_date' => $today,
            'start_time' => '13:00:00',
            'end_time' => '15:30:00',
            'purpose' => 'Praktikum Pengantar Machine Learning & Hands-on PyTorch.',
            'status' => 'disetujui',
            'approved_by' => $petugas->id,
            'approved_at' => now()->subHours(1),
        ]);

        // Reservasi 3: Menunggu (Mahasiswa - Besok 09:00 - 11:30)
        Reservation::create([
            'reservation_code' => 'RSV-20260915-003',
            'user_id' => $mahasiswa->id,
            'facility_id' => $fac3->id,
            'reservation_date' => $tomorrow,
            'start_time' => '09:00:00',
            'end_time' => '11:30:00',
            'purpose' => 'Briefing teknis dan gladi resik kompetisi Hackathon Mahasiswa 2026.',
            'status' => 'menunggu',
        ]);

        // Reservasi 4: Ditolak (Bentrok / alasan lain)
        Reservation::create([
            'reservation_code' => 'RSV-20260915-004',
            'user_id' => $mahasiswa->id,
            'facility_id' => $fac6->id,
            'reservation_date' => $tomorrow,
            'start_time' => '16:00:00',
            'end_time' => '18:00:00',
            'purpose' => 'Pertandingan persahabatan antar angkatan.',
            'status' => 'ditolak',
            'rejection_reason' => 'Jadwal bertepatan dengan agenda pemeliharaan rutin rumput sintetis oleh tim sarpras.',
            'approved_by' => $petugas->id,
            'approved_at' => now()->subMinutes(30),
        ]);

        // 4. Sample Data Laporan Kerusakan
        DamageReport::create([
            'report_code' => 'REP-20260914-001',
            'user_id' => $mahasiswa->id,
            'facility_id' => $fac1->id,
            'category' => 'kelistrikan_elektronik',
            'description' => 'Kabel HDMI proyektor di meja dosen putus/terkelupas dan port audio berdengung kencang saat kabel digerakkan.',
            'photo_path' => null,
            'status' => 'selesai',
            'resolution_notes' => 'Kabel HDMI telah diganti dengan kabel braided gold-plated baru, grounding audio panel telah diperbaiki.',
            'handled_by' => $petugas->id,
            'resolved_at' => now()->subHours(5),
        ]);

        DamageReport::create([
            'report_code' => 'REP-20260914-002',
            'user_id' => $dosen->id,
            'facility_id' => $fac5->id,
            'category' => 'alat_rusak_hilang',
            'description' => 'Solder station meja 3 korsleting mengeluarkan percikan api dan 2 unit oscilloscope digital perlu kalibrasi ulang.',
            'photo_path' => null,
            'status' => 'diproses',
            'resolution_notes' => 'Fasilitas diset dalam perbaikan. Suku cadang elemen pemanas solder sedang dipesan dari distributor.',
            'handled_by' => $petugas->id,
        ]);

        DamageReport::create([
            'report_code' => 'REP-20260914-003',
            'user_id' => $mahasiswa->id,
            'facility_id' => $fac2->id,
            'category' => 'fisik_bangunan',
            'description' => 'Gagang pintu Smart Classroom 204 goyang dan kunci magnetik otomatis kadang tidak dapat mengunci.',
            'photo_path' => null,
            'status' => 'baru',
        ]);
    }
}
