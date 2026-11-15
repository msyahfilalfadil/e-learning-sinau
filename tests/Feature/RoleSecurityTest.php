<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_any_protected_page(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/guru')->assertRedirect(route('login'));
        $this->get('/siswa')->assertRedirect(route('login'));
    }

    public function test_siswa_cannot_access_guru_and_is_redirected_to_siswa(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($siswa)->get('/guru');

        $response->assertRedirect(route('siswa'));
        $response->assertSessionHas('error');
    }

    public function test_siswa_cannot_access_admin_and_is_redirected_to_siswa(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($siswa)->get('/admin');

        $response->assertRedirect(route('siswa'));
        $response->assertSessionHas('error');
    }

    public function test_guru_cannot_access_siswa_and_is_redirected_to_guru(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get('/siswa');

        $response->assertRedirect(route('guru'));
        $response->assertSessionHas('error');
    }

    public function test_guru_cannot_access_admin_and_is_redirected_to_guru(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get('/admin');

        $response->assertRedirect(route('guru'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_access_all_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin BISA melihat semua role (Admin, Guru, Siswa)
        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/guru')->assertStatus(200);
        $this->actingAs($admin)->get('/siswa')->assertStatus(200);
    }

    public function test_each_user_can_access_their_own_space(): void
    {
        $guru  = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get('/guru')->assertStatus(200);
        $this->actingAs($siswa)->get('/siswa')->assertStatus(200);
    }
}
