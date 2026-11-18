<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasBarcodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_kelas_model_generates_barcode_c39_and_qrcode(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $kelas = Kelas::create([
            'guru_id' => $guru->id,
            'nama_kelas' => 'Matematika 10A',
            'mata_pelajaran' => 'Matematika',
            'kode_kelas' => 'MTH10A',
            'deskripsi' => 'Aljabar dan trigonometri',
        ]);

        $barcodeHtml = $kelas->barcodeHtml(2, 60);
        $this->assertNotEmpty($barcodeHtml);
        $this->assertStringContainsString('background-color:black', $barcodeHtml);

        $qrcodeHtml = $kelas->qrcodeHtml(4, 4);
        $this->assertNotEmpty($qrcodeHtml);
        $this->assertStringContainsString('background-color:black', $qrcodeHtml);
    }

    public function test_detail_kelas_page_renders_barcode_and_qrcode_successfully(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $kelas = Kelas::create([
            'guru_id' => $guru->id,
            'nama_kelas' => 'Fisika 10B',
            'mata_pelajaran' => 'Fisika',
            'kode_kelas' => 'FIS10B',
            'deskripsi' => 'Hukum Newton',
        ]);

        $response = $this->actingAs($guru)->get(route('kelas.show', $kelas->id));

        $response->assertStatus(200);
        $response->assertSee('Barcode & QR Code', false);
        $response->assertSee('FIS10B');
        $response->assertSee('Code 39', false);
        $response->assertSee('QR Code', false);
    }
}
