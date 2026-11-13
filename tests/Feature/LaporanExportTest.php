<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_laporan_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('laporan.kelas.pdf'));
        $response->assertRedirect(route('login'));

        $responseNilai = $this->get(route('laporan.nilai.pdf'));
        $responseNilai->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_download_laporan_kelas_pdf(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('laporan.kelas.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_authenticated_user_can_preview_laporan_kelas(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('laporan.kelas.preview'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_authenticated_user_can_download_laporan_nilai_pdf(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('laporan.nilai.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_authenticated_user_can_preview_laporan_nilai(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get(route('laporan.nilai.preview'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
