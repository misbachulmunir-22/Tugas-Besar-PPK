<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRedirectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_routes_without_redirect_loops(): void
    {
        // 1. Home
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // 2. Facilities
        $response = $this->get(route('public.facilities'));
        $response->assertStatus(200);

        // 3. Schedule
        $response = $this->get(route('public.schedule'));
        $response->assertStatus(200);

        // 4. Login Page
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // 5. Register Page
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_guest_accessing_protected_routes_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('petugas.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('pengguna.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_accessing_login_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'verified']);
        $response = $this->actingAs($admin)->get(route('login'));
        $response->assertRedirect(route('admin.dashboard'));

        $petugas = User::factory()->create(['role' => 'petugas', 'status' => 'verified']);
        $response = $this->actingAs($petugas)->get(route('login'));
        $response->assertRedirect(route('petugas.dashboard'));

        $pengguna = User::factory()->create(['role' => 'pengguna', 'status' => 'verified']);
        $response = $this->actingAs($pengguna)->get(route('login'));
        $response->assertRedirect(route('pengguna.dashboard'));
    }

    public function test_authenticated_user_can_still_view_public_home(): void
    {
        $pengguna = User::factory()->create(['role' => 'pengguna', 'status' => 'verified']);
        $response = $this->actingAs($pengguna)->get(route('home'));
        $response->assertStatus(200);
    }
}
