<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pengujian Terpadu User Story 1 s/d 17
 * Platform Khusus Fasilitas Kampus 2026
 */
class UserStoriesCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $petugas;

    protected User $pengguna;

    protected Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@kampus.ac.id',
            'role' => 'admin',
            'user_type' => 'staf',
            'status' => 'verified',
        ]);

        $this->petugas = User::factory()->create([
            'name' => 'Petugas Fasilitas',
            'email' => 'petugas@kampus.ac.id',
            'role' => 'petugas',
            'user_type' => 'staf',
            'status' => 'verified',
        ]);

        $this->pengguna = User::factory()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@kampus.ac.id',
            'role' => 'pengguna',
            'user_type' => 'mahasiswa',
            'identity_number' => 'NIM12345678',
            'status' => 'verified',
        ]);

        $this->facility = Facility::create([
            'name' => 'Laboratorium Komputer A',
            'code' => 'LAB-KOMP-A',
            'type' => 'laboratorium',
            'location' => 'Gedung C Lantai 2',
            'capacity' => 30,
            'description' => 'Lab komputer dengan 30 unit PC',
            'status' => 'aktif',
        ]);
    }

    /**
     * US 1: Sebagai pengunjung/pengguna, saya bisa melihat daftar fasilitas beserta status ketersediaannya per slot waktu (tersedia/tidak tersedia), tanpa melihat detail pemohon atau tujuan penggunaan.
     */
    public function test_us_01_public_can_view_facility_schedule_slots_without_private_details(): void
    {
        // Buat reservasi yang disetujui
        $res = Reservation::create([
            'reservation_code' => 'RSV-US01-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'purpose' => 'Praktikum Rahasia Pemohon Tertentu',
            'status' => 'disetujui',
        ]);

        $response = $this->get(route('public.schedule', [
            'facility_id' => $this->facility->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laboratorium Komputer A');
        // Memastikan nama pemohon dan tujuan privasi TIDAK tampil di jadwal publik
        $response->assertDontSee('Praktikum Rahasia Pemohon Tertentu');
        $response->assertDontSee($this->pengguna->name);
    }

    /**
     * US 2: Sebagai pengunjung/pengguna, saya bisa mencari fasilitas berdasarkan tipe/lokasi/kapasitas.
     */
    public function test_us_02_search_and_filter_facilities(): void
    {
        Facility::create([
            'name' => 'Aula Gedung Utama',
            'code' => 'AULA-01',
            'type' => 'aula',
            'location' => 'Gedung Pusat',
            'capacity' => 200,
            'status' => 'aktif',
        ]);

        // Filter berdasarkan tipe
        $responseType = $this->get(route('public.facilities', ['type' => 'aula']));
        $responseType->assertStatus(200);
        $responseType->assertSee('Aula Gedung Utama');
        $responseType->assertDontSee('Laboratorium Komputer A');

        // Filter berdasarkan kapasitas
        $responseCap = $this->get(route('public.facilities', ['min_capacity' => 100]));
        $responseCap->assertStatus(200);
        $responseCap->assertSee('Aula Gedung Utama');
        $responseCap->assertDontSee('Laboratorium Komputer A');

        // Filter berdasarkan lokasi
        $responseLoc = $this->get(route('public.facilities', ['location' => 'Gedung C']));
        $responseLoc->assertStatus(200);
        $responseLoc->assertSee('Laboratorium Komputer A');
        $responseLoc->assertDontSee('Aula Gedung Utama');
    }

    /**
     * US 3: Sebagai pengguna, saya bisa mengajukan reservasi pada rentang waktu tertentu dengan menyebutkan tujuan penggunaan.
     */
    public function test_us_03_pengguna_can_submit_reservation(): void
    {
        $response = $this->actingAs($this->pengguna)->post(route('pengguna.reservations.store'), [
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:30',
            'purpose' => 'Kegiatan workshop pemrograman web untuk mahasiswa tingkat akhir.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'status' => 'menunggu',
            'purpose' => 'Kegiatan workshop pemrograman web untuk mahasiswa tingkat akhir.',
        ]);
    }

    /**
     * US 4: Sebagai pengguna, saya bisa membatalkan reservasi saya sendiri sebelum batas waktu tertentu.
     */
    public function test_us_04_pengguna_can_cancel_own_reservation_before_start_time(): void
    {
        $reservation = Reservation::create([
            'reservation_code' => 'RSV-CANCEL-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'purpose' => 'Diskusi kelompok tugas besar.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->pengguna)->post(route('pengguna.reservations.cancel', $reservation), [
            'reason' => 'Ada perubahan agenda mendadak dari dosen pembimbing.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'dibatalkan_pengguna',
            'cancellation_reason' => 'Ada perubahan agenda mendadak dari dosen pembimbing.',
        ]);
    }

    /**
     * US 5: Sebagai pengguna, saya bisa melihat riwayat dan status reservasi saya, termasuk detail lengkap reservasi tersebut.
     */
    public function test_us_05_pengguna_can_view_reservation_history_and_details(): void
    {
        $reservation = Reservation::create([
            'reservation_code' => 'RSV-DETAIL-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Ujian sertifikasi kompetensi.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reservations.show', $reservation));
        $response->assertStatus(200);
        $response->assertSee('RSV-DETAIL-001');
        $response->assertSee('Ujian sertifikasi kompetensi.');
        $response->assertSee('Laboratorium Komputer A');
    }

    /**
     * US 6: Sebagai pengguna, saya bisa melaporkan kerusakan/masalah pada fasilitas tertentu (kategori, deskripsi, foto).
     */
    public function test_us_06_pengguna_can_submit_damage_report(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('kerusakan.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->pengguna)->post(route('pengguna.reports.store'), [
            'facility_id' => $this->facility->id,
            'category' => 'kelistrikan_elektronik',
            'description' => 'AC di ruang lab komputer mengeluarkan bunyi bising dan tidak dingin.',
            'photo' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('damage_reports', [
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'kelistrikan_elektronik',
            'status' => 'baru',
        ]);
    }

    /**
     * US 7: Sebagai pengguna, saya bisa melihat status laporan saya.
     */
    public function test_us_07_pengguna_can_view_report_status(): void
    {
        $report = DamageReport::create([
            'report_code' => 'REP-STATUS-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'fisik_bangunan',
            'description' => 'Gagang pintu lab rusak dan sulit dibuka.',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reports.show', $report));
        $response->assertStatus(200);
        $response->assertSee('REP-STATUS-001');
        $response->assertSee('Sedang Ditangani Teknisi');
    }

    /**
     * US 8: Sebagai petugas, saya bisa melihat dashboard/antrian reservasi dan laporan yang masih menunggu diproses, agar tidak ada yang terlewat.
     */
    public function test_us_08_petugas_dashboard_queue(): void
    {
        Reservation::create([
            'reservation_code' => 'RSV-ANTRI-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
            'purpose' => 'Antrian reservasi baru.',
            'status' => 'menunggu',
        ]);

        DamageReport::create([
            'report_code' => 'REP-ANTRI-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'kebersihan',
            'description' => 'Antrian laporan kebersihan.',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Laboratorium Komputer A');
        $response->assertSee('kebersihan');
    }

    /**
     * US 9: Sebagai petugas, saya bisa menyetujui/menolak reservasi yang masuk secara manual; sistem mencegah persetujuan reservasi yang bentrok jadwal pada fasilitas yang sama.
     */
    public function test_us_09_petugas_approve_reject_and_conflict_prevention(): void
    {
        $res1 = Reservation::create([
            'reservation_code' => 'RSV-APPROVED-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Reservasi pertama.',
            'status' => 'menunggu',
        ]);

        // Petugas menyetujui res1
        $response = $this->actingAs($this->petugas)->post(route('petugas.reservations.approve', $res1));
        $response->assertRedirect();
        $this->assertEquals('disetujui', $res1->fresh()->status);

        // Buat res2 yang bentrok dengan res1 (09:00 - 11:00)
        $res2 = Reservation::create([
            'reservation_code' => 'RSV-CONFLICT-002',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'purpose' => 'Reservasi kedua yang bentrok.',
            'status' => 'menunggu',
        ]);

        // Mencoba menyetujui res2 -> HARUS DICEGAH OLEH SISTEM
        $responseConflict = $this->actingAs($this->petugas)->post(route('petugas.reservations.approve', $res2));
        $responseConflict->assertRedirect();
        $this->assertEquals('menunggu', $res2->fresh()->status);

        // Petugas menolak res2 dengan alasan
        $responseReject = $this->actingAs($this->petugas)->post(route('petugas.reservations.reject', $res2), [
            'rejection_reason' => 'Jadwal bertabrakan dengan kegiatan praktikum lain.',
        ]);
        $responseReject->assertRedirect();
        $this->assertEquals('ditolak', $res2->fresh()->status);
        $this->assertEquals('Jadwal bertabrakan dengan kegiatan praktikum lain.', $res2->fresh()->rejection_reason);
    }

    /**
     * US 10: Sebagai petugas, saya bisa membatalkan reservasi yang sudah disetujui dalam kondisi mendesak (mis. fasilitas mendadak tidak bisa dipakai), dengan mencantumkan alasan pembatalan.
     */
    public function test_us_10_petugas_emergency_cancel(): void
    {
        $res = Reservation::create([
            'reservation_code' => 'RSV-EMERGENCY-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'purpose' => 'Seminar umum.',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.reservations.emergency_cancel', $res), [
            'cancellation_reason' => 'Ruangan mengalami pemadaman listrik mendadak untuk perbaikan panel utama.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('dibatalkan_petugas', $res->fresh()->status);
        $this->assertEquals('Ruangan mengalami pemadaman listrik mendadak untuk perbaikan panel utama.', $res->fresh()->cancellation_reason);
    }

    /**
     * US 11: Sebagai petugas, saya bisa mengubah status laporan (baru/diproses/selesai/ditolak) beserta catatan resolusi saat laporan ditutup.
     */
    public function test_us_11_petugas_update_report_status_and_resolution_notes(): void
    {
        $report = DamageReport::create([
            'report_code' => 'REP-RESOLVE-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'kelistrikan_elektronik',
            'description' => 'Proyektor mati total.',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.reports.update_status', $report), [
            'status' => 'selesai',
            'resolution_notes' => 'Kabel power dan lampu modul proyektor telah diganti dengan suku cadang baru.',
            'update_facility_status' => 'aktif',
        ]);

        $response->assertRedirect();
        $this->assertEquals('selesai', $report->fresh()->status);
        $this->assertEquals('Kabel power dan lampu modul proyektor telah diganti dengan suku cadang baru.', $report->fresh()->resolution_notes);
        $this->assertNotNull($report->fresh()->resolved_at);
    }

    /**
     * US 12: Sebagai petugas, saya bisa menandai fasilitas berstatus 'dalam perbaikan' terkait laporan kerusakan yang sedang ditangani, dan mengembalikannya ke status aktif setelah selesai diperbaiki.
     */
    public function test_us_12_petugas_toggle_facility_maintenance_status(): void
    {
        // Ubah menjadi dalam_perbaikan
        $response = $this->actingAs($this->petugas)->post(route('petugas.facilities.toggle_status', $this->facility), [
            'status' => 'dalam_perbaikan',
        ]);
        $response->assertRedirect();
        $this->assertEquals('dalam_perbaikan', $this->facility->fresh()->status);

        // Kembalikan ke aktif
        $responseActive = $this->actingAs($this->petugas)->post(route('petugas.facilities.toggle_status', $this->facility), [
            'status' => 'aktif',
        ]);
        $responseActive->assertRedirect();
        $this->assertEquals('aktif', $this->facility->fresh()->status);
    }

    /**
     * US 13: Sebagai admin, saya bisa mendaftarkan akun petugas secara langsung (petugas tidak melakukan registrasi mandiri dalam kondisi apa pun).
     */
    public function test_us_13_admin_direct_staff_registration(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Lapangan Baru',
            'email' => 'petugas.baru@kampus.ac.id',
            'role' => 'petugas',
            'user_type' => 'staf',
            'identity_number' => 'STAF-8899',
            'phone' => '081299998888',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'petugas.baru@kampus.ac.id',
            'role' => 'petugas',
            'status' => 'verified',
        ]);
    }

    /**
     * US 14: Sebagai admin, saya bisa mendaftarkan akun pengguna (mahasiswa/dosen/staf) secara langsung tanpa melalui form registrasi mandiri.
     */
    public function test_us_14_admin_direct_user_registration(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Dosen Pengampu',
            'email' => 'dosen@kampus.ac.id',
            'role' => 'pengguna',
            'user_type' => 'dosen',
            'identity_number' => 'NIP19870101',
            'phone' => '081377776666',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'dosen@kampus.ac.id',
            'role' => 'pengguna',
            'user_type' => 'dosen',
            'status' => 'verified',
        ]);
    }

    /**
     * US 15: Sebagai admin, saya bisa memverifikasi atau menolak akun pengguna hasil registrasi mandiri (jika diimplementasikan) sebelum akun tersebut dapat digunakan untuk login.
     */
    public function test_us_15_admin_verify_and_reject_user_account(): void
    {
        // 1. Pengguna melakukan registrasi mandiri
        $registerResponse = $this->post(route('register'), [
            'name' => 'Mahasiswa Mandiri',
            'email' => 'mandiri.mhs@kampus.ac.id',
            'user_type' => 'mahasiswa',
            'identity_number' => 'NIM99990001',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $registerResponse->assertRedirect(route('login'));
        $registerResponse->assertSessionHas('warning');

        // Pastikan status akun di database adalah 'pending'
        $this->assertDatabaseHas('users', [
            'email' => 'mandiri.mhs@kampus.ac.id',
            'status' => 'pending',
        ]);

        $pendingUser = User::where('email', 'mandiri.mhs@kampus.ac.id')->first();
        $this->assertNotNull($pendingUser);

        // 2. Coba login saat status pending -> Ditolak oleh AuthController
        $loginResponse = $this->post(route('login'), [
            'email' => 'mandiri.mhs@kampus.ac.id',
            'password' => 'password123',
        ]);
        $loginResponse->assertSessionHas('warning');
        $this->assertGuest();

        // 3. Admin memverifikasi akun pengguna
        $verifyResponse = $this->actingAs($this->admin)->post(route('admin.users.verify', $pendingUser));
        $verifyResponse->assertRedirect();
        $this->assertEquals('verified', $pendingUser->fresh()->status);

        // Logout admin dari sesi tes sebelum mencoba login sebagai pengguna
        auth()->logout();

        // 4. Setelah verifikasi -> Pengguna berhasil login ke dashboard
        $loginVerified = $this->post(route('login'), [
            'email' => 'mandiri.mhs@kampus.ac.id',
            'password' => 'password123',
        ]);
        $loginVerified->assertRedirect(route('pengguna.dashboard'));
        $this->assertAuthenticatedAs($pendingUser);
    }

    /**
     * US 16: Sebagai admin, saya bisa mengelola data fasilitas (tambah/edit/nonaktifkan).
     */
    public function test_us_16_admin_manage_facilities_crud(): void
    {
        // 1. Tambah fasilitas
        $createResponse = $this->actingAs($this->admin)->post(route('admin.facilities.store'), [
            'name' => 'Ruang Seminar B',
            'code' => 'RS-B',
            'type' => 'ruang_kelas',
            'location' => 'Gedung Pascasarjana',
            'capacity' => 50,
            'description' => 'Ruang seminar ber-AC dengan proyektor.',
            'status' => 'aktif',
        ]);
        $createResponse->assertRedirect();
        $newFacility = Facility::where('code', 'RS-B')->first();
        $this->assertNotNull($newFacility);

        // 2. Edit fasilitas
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.facilities.update', $newFacility), [
            'name' => 'Ruang Seminar B Updated',
            'code' => 'RS-B',
            'type' => 'ruang_kelas',
            'location' => 'Gedung Pascasarjana Lt. 3',
            'capacity' => 60,
            'description' => 'Kapasitas telah ditingkatkan menjadi 60 kursi.',
            'status' => 'aktif',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('Ruang Seminar B Updated', $newFacility->fresh()->name);
        $this->assertEquals(60, $newFacility->fresh()->capacity);

        // 3. Hapus / Nonaktifkan fasilitas
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.facilities.destroy', $newFacility));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('facilities', ['id' => $newFacility->id]);
    }

    /**
     * US 17: Sebagai admin, saya bisa melihat dan mengekspor (CSV/Excel/PDF) rekap okupansi fasilitas dan frekuensi kerusakan per fasilitas/lokasi.
     */
    public function test_us_17_admin_rekap_occupancy_and_damage_export(): void
    {
        // Buat data reservasi disetujui untuk dihitung okupansinya
        Reservation::create([
            'reservation_code' => 'RSV-REKAP-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => Carbon::now()->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '11:00:00', // 3 jam
            'purpose' => 'Pelatihan.',
            'status' => 'disetujui',
        ]);

        // Buat data kerusakan
        DamageReport::create([
            'report_code' => 'REP-REKAP-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'kelistrikan_elektronik',
            'description' => 'Lampu kedip-kedip.',
            'status' => 'selesai',
        ]);

        // 1. Tampilan Rekap Web
        $responseWeb = $this->actingAs($this->admin)->get(route('admin.rekap.index'));
        $responseWeb->assertStatus(200);
        $responseWeb->assertSee('Laboratorium Komputer A');

        // 2. Ekspor CSV
        $responseCsv = $this->actingAs($this->admin)->get(route('admin.rekap.export_csv'));
        $responseCsv->assertStatus(200);
        $responseCsv->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 3. Tampilan Print PDF
        $responsePrint = $this->actingAs($this->admin)->get(route('admin.rekap.print'));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('Laboratorium Komputer A');
    }
}
