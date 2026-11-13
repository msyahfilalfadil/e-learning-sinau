<?php

namespace App\Exports;

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
     * Berikan minimal 10 contoh baris data seperti ketentuan praktikum
     */
    public function array(): array
    {
        return [
            ['Matematika 10A', 'Matematika', 'MTH10A', 'Pembelajaran aljabar dan trigonometri dasar'],
            ['Fisika 10B', 'Fisika', 'FIS10B', 'Konsep gerak lurus, vektor, dan hukum Newton'],
            ['Kimia 10A', 'Kimia', 'KMA10A', 'Struktur atom dan ikatan kimia'],
            ['Biologi 10A', 'Biologi', 'BIO10A', 'Keanekaragaman hayati dan ekosistem'],
            ['Bahasa Indonesia 10C', 'Bahasa Indonesia', 'IND10C', 'Teks eksposisi dan negosiasi'],
            ['Bahasa Inggris 10A', 'Bahasa Inggris', 'ENG10A', 'Descriptive text and personal letters'],
            ['Sejarah Indonesia 10B', 'Sejarah', 'SEJ10B', 'Zaman praaksara dan masuknya Hindu-Buddha'],
            ['Geografi 10A', 'Geografi', 'GEO10A', 'Prinsip geografi dan dinamika litosfer'],
            ['Ekonomi 10B', 'Ekonomi', 'EKO10B', 'Kelangkaan dan sistem ekonomi'],
            ['Sosiologi 10A', 'Sosiologi', 'SOS10A', 'Interaksi sosial dan institusi kemasyarakatan'],
        ];
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

