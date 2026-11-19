<?php

namespace App\Exports;

use App\Models\Kelas;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KelasTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    /**
     * Ambil 5 data contoh nyata langsung dari database
     */
    public function collection(): Enumerable
    {
        return Kelas::with('guru')->orderBy('id', 'asc')->take(5)->get();
    }

    public function title(): string
    {
        return 'Template Kelas';
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Kelas',
            'Mata Pelajaran',
            'Kode Kelas',
            'Guru Pengampu',
            'Deskripsi',
            'Tanggal Dibuat',
        ];
    }

    protected int $row = 0;

    public function map($kelas): array
    {
        $this->row++;
        return [
            $this->row,
            $kelas->nama_kelas,
            $kelas->mata_pelajaran,
            $kelas->kode_kelas,
            $kelas->guru->name ?? 'Guru Pengampu',
            $kelas->deskripsi ?? '-',
            $kelas->created_at ? $kelas->created_at->format('d/m/Y') : date('d/m/Y'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 32,
            'C' => 26,
            'D' => 15,
            'E' => 28,
            'F' => 50,
            'G' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1a7a3e']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Judul template (baris 1)
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'TEMPLATE DATA KELAS — SINAU');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF1a7a3e']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Sub judul / petunjuk (baris 2)
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Petunjuk: Format resmi impor/ekspor data kelas • Dicetak: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF777777']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Baris kosong pemisah (baris 3)
                $sheet->mergeCells('A3:G3');
                $sheet->getRowDimension(3)->setRowHeight(8);

                // Header kolom (baris 4)
                $sheet->getStyle('A4:G4')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1a7a3e']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(26);

                // Baris data (baris 5 ke atas): zebra striping
                for ($i = 5; $i <= $lastRow + 3; $i++) {
                    $color = ($i % 2 === 0) ? 'FFF0FBF4' : 'FFFFFFFF';
                    $sheet->getStyle("A{$i}:G{$i}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                    ]);
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                // Posisi teks (No, Kode, Tanggal di tengah)
                $sheet->getStyle("A5:A" . ($lastRow + 3))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("D5:D" . ($lastRow + 3))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("G5:G" . ($lastRow + 3))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Border tabel
                $sheet->getStyle("A4:G" . ($lastRow + 3))->applyFromArray([
                    'borders' => [
                        'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1a7a3e']],
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['argb' => 'FFD4EDD9']],
                    ],
                ]);

                $sheet->freezePane('A5');
            },
        ];
    }
}
