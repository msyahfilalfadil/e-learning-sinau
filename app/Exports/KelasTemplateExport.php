<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KelasTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * Ambil 5 data contoh nyata langsung dari database
     */
    public function array(): array
    {
        $kelasList = Kelas::orderBy('id', 'asc')->take(5)->get();

        $rows = [];
        foreach ($kelasList as $kelas) {
            $rows[] = [
                $kelas->nama_kelas,
                $kelas->mata_pelajaran,
                $kelas->kode_kelas,
                $kelas->deskripsi ?? '-',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'nama_kelas',
            'mata_pelajaran',
            'kode_kelas',
            'deskripsi',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 26,
            'B' => 22,
            'C' => 16,
            'D' => 45,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1a7a3e']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
