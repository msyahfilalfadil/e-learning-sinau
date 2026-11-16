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
     * Aturan:
     * 1. Jika kolom ada yang kosong -> Batal semua.
     * 2. Jika kode_kelas sama DAN isinya beda -> ERROR / Batal semua (konflik data).
     * 3. Jika kode_kelas sama DAN isinya sama persis -> Tetap lanjut (dilewati agar tidak duplikat di DB).
     */
    public function collection(Collection $rows): void
    {
        $validData    = [];
        $errors       = [];
        $seenInFile   = []; // Menyimpan data unik yang sudah dibaca dalam file
        $rowNumber    = 1;  // Baris 1 adalah header

        // Ambil data yang sudah ada di database untuk dicocokkan
        $existingClasses = Kelas::all()->keyBy(fn($item) => strtoupper(trim($item->kode_kelas)));

        foreach ($rows as $row) {
            $rowNumber++;

            $nama      = trim($row['nama_kelas'] ?? '');
            $mapel     = trim($row['mata_pelajaran'] ?? '');
            $kode      = strtoupper(trim($row['kode_kelas'] ?? ''));
            $deskripsi = trim($row['deskripsi'] ?? '');

            // Abaikan jika baris benar-benar kosong total
            if ($nama === '' && $mapel === '' && $kode === '' && $deskripsi === '') {
                continue;
            }

            // 1. Cek kelengkapan 4 kolom
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

            // 2. CEK TERHADAP DATABASE:
            if ($existingClasses->has($kode)) {
                $dbKelas = $existingClasses->get($kode);
                
                // Cek apakah data lainnya BERBEDA
                $isDifferent = (
                    strcasecmp($dbKelas->nama_kelas, $nama) !== 0 ||
                    strcasecmp($dbKelas->mata_pelajaran, $mapel) !== 0 ||
                    strcasecmp(trim($dbKelas->deskripsi ?? ''), $deskripsi) !== 0
                );

                if ($isDifferent) {
                    // Kode sama TAPI data lain beda -> ERROR
                    $errors[] = "Baris {$rowNumber} (kode kelas '{$kode}' sudah ada di sistem tetapi datanya berbeda)";
                    continue;
                } else {
                    // Kode sama DAN semua data persis sama -> Lanjut (abaikan insert ulang)
                    continue;
                }
            }

            // 3. CEK DI DALAM FILE ITU SENDIRI (Jika ada kode kembar di file):
            if (isset($seenInFile[$kode])) {
                $prev = $seenInFile[$kode];

                $isDifferentInFile = (
                    strcasecmp($prev['nama_kelas'], $nama) !== 0 ||
                    strcasecmp($prev['mata_pelajaran'], $mapel) !== 0 ||
                    strcasecmp($prev['deskripsi'], $deskripsi) !== 0
                );

                if ($isDifferentInFile) {
                    // Di dalam file ada kode sama TAPI isinya beda -> ERROR
                    $errors[] = "Baris {$rowNumber} (kode kelas '{$kode}' sama dengan baris {$prev['row']} tetapi isinya berbeda)";
                    continue;
                } else {
                    // Kode sama DAN isi persis sama di dalam file -> Lanjut (lewati baris kembar kedua)
                    continue;
                }
            }

            // Catat baris data baru
            $seenInFile[$kode] = [
                'row'            => $rowNumber,
                'nama_kelas'     => $nama,
                'mata_pelajaran' => $mapel,
                'deskripsi'      => $deskripsi,
            ];

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

        // JIKA ADA SALAH SATU BARIS YANG KONFLIK ATAU TIDAK LENGKAP:
        if (!empty($errors)) {
            $petunjuk = implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $petunjuk .= ' dan ' . (count($errors) - 5) . ' masalah lainnya';
            }

            throw new Exception("Seluruh impor dibatalkan karena: {$petunjuk}. Silakan perbaiki data dan unggah kembali.");
        }

        // JIKA SEMUA LOLOS:
        if (!empty($validData)) {
            Kelas::insert($validData);
            $this->imported = count($validData);
        }
    }
}
