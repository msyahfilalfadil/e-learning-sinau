<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class KelasImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    /**
     * Membaca seluruh data sekaligus ke dalam Collection.
     * Menerapkan prinsip ALL-OR-NOTHING:
     * - Batalkan jika ada kolom kosong (belum tuntas)
     * - Batalkan jika ada kode_kelas yang sama (duplikat di file atau sudah ada di database)
     */
    public function collection(Collection $rows): void
    {
        $validData    = [];
        $errors       = [];
        $seenKodeInFile = []; // Mencatat kode yang muncul di file Excel untuk cek duplikat internal
        $rowNumber    = 1;    // Baris 1 adalah header di file Excel

        // 1. Ambil semua kode_kelas yang SUDAH ADA di database
        $existingDbCodes = Kelas::pluck('kode_kelas')->map(fn($k) => strtoupper(trim($k)))->toArray();

        foreach ($rows as $row) {
            $rowNumber++;

            $nama      = trim($row['nama_kelas'] ?? '');
            $mapel     = trim($row['mata_pelajaran'] ?? '');
            $kode      = strtoupper(trim($row['kode_kelas'] ?? ''));
            $deskripsi = trim($row['deskripsi'] ?? '');

            // Abaikan jika baris benar-benar kosong total (sisa baris kosong di bawah file)
            if ($nama === '' && $mapel === '' && $kode === '' && $deskripsi === '') {
                continue;
            }

            // A. Cek kolom yang belum tuntas diisi
            $missingFields = [];
            if ($nama === '') {
                $missingFields[] = 'Nama Kelas';
            }
            if ($mapel === '') {
                $missingFields[] = 'Mata Pelajaran';
            }
            if ($kode === '') {
                $missingFields[] = 'Kode Kelas';
            }
            if ($deskripsi === '') {
                $missingFields[] = 'Deskripsi';
            }

            if (!empty($missingFields)) {
                $errors[] = "Baris {$rowNumber} (kolom " . implode(', ', $missingFields) . " belum tuntas diisi)";
                continue;
            }

            // B. Cek apakah kode_kelas SUDAH ADA di database sebelumnya
            if (in_array($kode, $existingDbCodes, true)) {
                $errors[] = "Baris {$rowNumber} (kode kelas '{$kode}' sudah terdaftar di sistem)";
                continue;
            }

            // C. Cek apakah kode_kelas DUPLIKAT di dalam file Excel itu sendiri
            if (in_array($kode, $seenKodeInFile, true)) {
                $errors[] = "Baris {$rowNumber} (kode kelas '{$kode}' kembar/duplikat di dalam file Excel)";
                continue;
            }

            // Catat kode yang sudah diperiksa
            $seenKodeInFile[] = $kode;

            // Simpan ke antrean data yang siap diinsert
            $validData[] = [
                'nama_kelas'     => $nama,
                'mata_pelajaran' => $mapel,
                'kode_kelas'     => $kode,
                'guru_id'        => auth()->id(),
                'deskripsi'      => $deskripsi,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // JIKA ADA SALAH SATU BARIS YANG ERROR (KOSONG ATAU KODE KELAS KEMBAR):
        // Gagalkan seluruh proses impor! Semua data yang lain tidak boleh masuk.
        if (!empty($errors)) {
            $petunjuk = implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $petunjuk .= ' dan ' . (count($errors) - 5) . ' masalah lainnya';
            }

            throw new Exception("Seluruh impor dibatalkan karena: {$petunjuk}. Silakan perbaiki data dan unggah kembali.");
        }

        // JIKA SEMUA DATA LOLOS VALIDASI:
        // Masukkan seluruhnya ke tabel database secara serentak
        if (!empty($validData)) {
            Kelas::insert($validData);
            $this->imported = count($validData);
        }
    }
}
