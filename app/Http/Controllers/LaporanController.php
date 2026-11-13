<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Exports\KelasExport;
use App\Exports\NilaiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
   
    public function exportPdfKelas()
    {
        $kelasList = Kelas::with(['guru', 'materi', 'kuis'])->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf.kelas', compact('kelasList'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('laporan-kelas-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Stream / Preview Laporan Data Kelas di Browser.
     */
    public function streamPdfKelas()
    {
        $kelasList = Kelas::with(['guru', 'materi', 'kuis'])->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf.kelas', compact('kelasList'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->stream('laporan-kelas-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Ekspor Laporan Rekap Nilai Kuis Siswa ke PDF (Download).
     * Menerapkan eager loading with(['siswa', 'kuis.kelas']).
     */
    public function exportPdfNilai()
    {
        $nilaiList = Nilai::with(['siswa', 'kuis.kelas'])->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf.nilai', compact('nilaiList'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('laporan-nilai-kuis-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Stream / Preview Laporan Rekap Nilai Kuis di Browser.
     */
    public function streamPdfNilai()
    {
        $nilaiList = Nilai::with(['siswa', 'kuis.kelas'])->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf.nilai', compact('nilaiList'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->stream('laporan-nilai-kuis-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Laporan Data Kelas ke Excel (.xlsx)
     */
    public function exportExcelKelas()
    {
        return Excel::download(
            new KelasExport,
            'laporan-kelas-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export Laporan Rekap Nilai Kuis ke Excel (.xlsx)
     */
    public function exportExcelNilai()
    {
        return Excel::download(
            new NilaiExport,
            'laporan-nilai-kuis-' . date('Y-m-d') . '.xlsx'
        );
    }
}

