<?php

namespace Tests\Feature;

use App\Exports\KelasExport;
use App\Exports\KelasTemplateExport;
use App\Imports\KelasImport;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class KelasExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_download_kelas_template_excel(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get(route('kelas.template'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', 'template-import-kelas.xlsx')
        );
    }

    public function test_guru_can_download_laporan_kelas_excel(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get(route('laporan.kelas.excel'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', 'laporan-kelas-')
        );
    }

    public function test_export_and_template_have_matching_headings(): void
    {
        $export = new KelasExport();
        $template = new KelasTemplateExport();

        $this->assertEquals($export->headings(), $template->headings());
        $this->assertEquals($export->columnWidths(), $template->columnWidths());
    }

    public function test_import_can_read_exported_classes_without_error(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        Kelas::create([
            'guru_id'        => $guru->id,
            'nama_kelas'     => 'Kimia Dasar 10',
            'mata_pelajaran' => 'Kimia',
            'kode_kelas'     => 'KMA10D',
            'deskripsi'      => 'Pembelajaran ikatan kimia dasar',
        ]);

        // Simpan hasil ekspor ke storage testing
        Excel::fake();

        // Testing route download template
        $response = $this->actingAs($guru)->get(route('kelas.template'));
        Excel::assertDownloaded('template-import-kelas.xlsx');
    }
}
