<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesAndViewsTest extends TestCase
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
            'role' => 'admin',
            'user_type' => 'staf',
            'status' => 'verified',
        ]);

        $this->petugas = User::factory()->create([
            'role' => 'petugas',
            'user_type' => 'staf',
            'status' => 'verified',
        ]);

        $this->pengguna = User::factory()->create([
            'role' => 'pengguna',
            'user_type' => 'mahasiswa',
            'status' => 'verified',
        ]);

        $this->facility = Facility::create([
            'name' => 'Ruang Teori 101',
            'code' => 'RT-101',
            'type' => 'ruang_kelas',
            'location' => 'Gedung A',
            'capacity' => 40,
            'status' => 'aktif',
        ]);
    }

    public function test_admin_facilities_views(): void
    {
        // 1. Facilities Index
        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.facilities.index');
        $response->assertSee('Ruang Teori 101');

        // 2. Facilities Create
        $response = $this->actingAs($this->admin)->get(route('admin.facilities.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.facilities.create');

        // 3. Facilities Edit
        $response = $this->actingAs($this->admin)->get(route('admin.facilities.edit', $this->facility));
        $response->assertStatus(200);
        $response->assertViewIs('admin.facilities.edit');

        // 4. Admin Rekap
        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.rekap');

        // 5. Admin Print
        $response = $this->actingAs($this->admin)->get(route('admin.rekap.print'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.print');
    }

    public function test_petugas_views(): void
    {
        // 1. Petugas Dashboard
        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $response->assertStatus(200);

        // 2. Petugas Reservations Index
        $response = $this->actingAs($this->petugas)->get(route('petugas.reservations.index'));
        $response->assertStatus(200);
        $response->assertViewIs('petugas.reservations.index');

        // 3. Petugas Reports Index
        $response = $this->actingAs($this->petugas)->get(route('petugas.reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('petugas.reports.index');
    }

    public function test_pengguna_views(): void
    {
        // 1. Pengguna Dashboard
        $response = $this->actingAs($this->pengguna)->get(route('pengguna.dashboard'));
        $response->assertStatus(200);

        // 2. Pengguna Reservations Index & Create
        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reservations.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reservations.index');

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reservations.create', ['facility_id' => $this->facility->id]));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reservations.create');

        // 3. Pengguna Reservation Show
        $reservation = Reservation::create([
            'reservation_code' => 'RSV-TEST-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'reservation_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Kegiatan belajar bersama kelompok.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reservations.show', $reservation));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reservations.show');

        // 4. Pengguna Reports Index, Create, Show
        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reports.index');

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reports.create'));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reports.create');

        $report = DamageReport::create([
            'report_code' => 'REP-TEST-001',
            'user_id' => $this->pengguna->id,
            'facility_id' => $this->facility->id,
            'category' => 'kelistrikan_elektronik',
            'description' => 'Saklar lampu berbunyi gemeretak.',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->pengguna)->get(route('pengguna.reports.show', $report));
        $response->assertStatus(200);
        $response->assertViewIs('pengguna.reports.show');
    }
}
