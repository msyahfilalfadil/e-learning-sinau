<?php

namespace App\Exports;

use App\Models\Nilai;
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

class NilaiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    public function collection(): Enumerable
    {
        return Nilai::with(['siswa', 'kuis.kelas'])->latest()->get();
    }

    public function title(): string
    {
        return 'Rekap Nilai';
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Siswa',
            'Kuis',
            'Kelas',
            'Nilai',
            'Grade',
            'Status',
            'Tanggal',
        ];
    }

    protected int $row = 0;

    public function map($nilai): array
    {
        $this->row++;
        return [
            $this->row,
            $nilai->siswa->name ?? '-',
            $nilai->kuis->judul ?? '-',
            $nilai->kuis->kelas->nama_kelas ?? '-',
            $nilai->nilai,
            $nilai->grade ?? '-',
            ucfirst($nilai->status),
            $nilai->created_at->format('d/m/Y'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 28,
            'C' => 32,
            'D' => 26,
            'E' => 10,
            'F' => 10,
            'G' => 16,
            'H' => 18,
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

                // Judul laporan
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'LAPORAN REKAP NILAI KUIS — SINAU');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF1a7a3e']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Sub judul
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Dicetak: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF777777']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Baris kosong
                $sheet->mergeCells('A3:H3');
                $sheet->getRowDimension(3)->setRowHeight(8);

                // Header (baris 4)
                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1a7a3e']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(26);

                // Baris data: zebra, tinggi lapang
                for ($i = 5; $i <= $lastRow + 3; $i++) {
                    $color = ($i % 2 === 0) ? 'FFF0FBF4' : 'FFFFFFFF';
                    $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                // Nomor urut rata tengah
                $sheet->getStyle("A5:A" . ($lastRow + 3))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Nilai: bold + tengah
                $sheet->getStyle("E5:E" . ($lastRow + 3))->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Grade, Status, Tanggal: tengah
                $sheet->getStyle("F5:H" . ($lastRow + 3))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Border luar tebal, dalam tipis
                $sheet->getStyle("A4:H" . ($lastRow + 3))->applyFromArray([
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
