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
     * Menerapkan prinsip ALL-OR-NOTHING (Semua masuk ATAU semua batal jika ada yang belum tuntas).
     */
    public function collection(Collection $rows): void
    {
        $validData = [];
        $errors    = [];
        $rowNumber = 1; // Baris 1 adalah header di file Excel

        foreach ($rows as $row) {
            $rowNumber++;

            $nama      = trim($row['nama_kelas'] ?? '');
            $mapel     = trim($row['mata_pelajaran'] ?? '');
            $kode      = trim($row['kode_kelas'] ?? '');
            $deskripsi = trim($row['deskripsi'] ?? '');

            // Abaikan jika baris benar-benar kosong total (misal sisa baris kosong di bawah file)
            if ($nama === '' && $mapel === '' && $kode === '' && $deskripsi === '') {
                continue;
            }

            // Cek apakah ada field yang belum tuntas
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

            // Jika ada salah satu yang tidak lengkap, catat ke daftar error
            if (!empty($missingFields)) {
                $errors[] = "Baris {$rowNumber} (kolom " . implode(', ', $missingFields) . " belum tuntas diisi)";
                continue;
            }

            // Simpan sementara ke antrean data yang siap diinsert
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

        // JIKA ADA SALAH SATU BARIS YANG TIDAK LENGKAP:
        // Gagalkan seluruh proses impor! Semua data yang lengkap pun TIDAK boleh masuk.
        if (!empty($errors)) {
            $petunjuk = implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $petunjuk .= ' dan ' . (count($errors) - 5) . ' baris lainnya';
            }

            throw new Exception("Seluruh impor dibatalkan karena ada data yang belum tuntas dimasukkan: {$petunjuk}. Silakan lengkapi file Excel dan unggah kembali.");
        }

        // JIKA SEMUA DATA LENGKAP 100%:
        // Baru masukkan semua ke database
        if (!empty($validData)) {
            Kelas::insert($validData);
            $this->imported = count($validData);
        }
    }
}
