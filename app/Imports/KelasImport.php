<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class KelasImport implements ToCollection
{
    public int $imported = 0;

    /**
     * Membaca seluruh data sekaligus ke dalam Collection.
     * Mendukung:
     * 1. File Template Impor (Row 1-3 Judul, Row 4 Header, Row 5+ Data)
     * 2. File Hasil Ekspor Kelas (Row 1-3 Judul, Row 4 Header, Row 5+ Data)
     * 3. File Excel polos (Row 1 Header, Row 2+ Data)
     * 
     * Aturan Validasi:
     * - Kolom Nama Kelas, Mata Pelajaran, Kode Kelas, Deskripsi wajib diisi.
     * - Kode kelas sama & isi beda -> DITOLAK (Semua baris batal / All-or-Nothing).
     * - Kode kelas sama & isi sama persis -> DILEWATI (tanpa error).
     */
    public function collection(Collection $rows): void
    {
        $validData       = [];
        $errors          = [];
        $seenInFile      = [];
        $headerFound     = false;

        // Indeks kolom default sesuai 7 kolom ekspor/template:
        // Index 0: No., 1: Nama Kelas, 2: Mata Pelajaran, 3: Kode Kelas, 4: Guru, 5: Deskripsi, 6: Tanggal
        $colNama      = 1;
        $colMapel     = 2;
        $colKode      = 3;
        $colDeskripsi = 5;

        // Ambil data database untuk validasi duplikat
        $existingClasses = Kelas::all()->keyBy(fn($item) => strtoupper(trim((string)$item->kode_kelas)));

        $physicalRow = 0;

        foreach ($rows as $row) {
            $physicalRow++;

            // Ubah row ke array indexed [0, 1, 2, ...]
            $values = is_array($row) ? array_values($row) : $row->values()->toArray();

            // Normalisasi teks untuk deteksi header
            $normalizedRow = array_map(fn($v) => strtolower(trim((string)$v)), $values);

            // Cek apakah baris ini adalah BARIS HEADER
            $hasNamaHeader = false;
            $hasKodeHeader = false;
            foreach ($normalizedRow as $cell) {
                if (in_array($cell, ['nama kelas', 'nama_kelas', 'nama', 'kelas'])) {
                    $hasNamaHeader = true;
                }
                if (in_array($cell, ['kode kelas', 'kode_kelas', 'kode'])) {
                    $hasKodeHeader = true;
                }
            }

            if (!$headerFound && $hasNamaHeader && $hasKodeHeader) {
                $headerFound = true;

                // Petakan letak indeks kolom secara dinamis berdasarkan baris header
                foreach ($normalizedRow as $idx => $cell) {
                    if (in_array($cell, ['nama kelas', 'nama_kelas'])) {
                        $colNama = $idx;
                    } elseif (in_array($cell, ['mata pelajaran', 'mata_pelajaran', 'mapel'])) {
                        $colMapel = $idx;
                    } elseif (in_array($cell, ['kode kelas', 'kode_kelas', 'kode'])) {
                        $colKode = $idx;
                    } elseif (in_array($cell, ['deskripsi', 'keterangan'])) {
                        $colDeskripsi = $idx;
                    }
                }
                continue; // Lanjut ke baris data setelah header
            }

            // Jika header belum ditemukan, cek apakah baris ini judul/keterangan atas
            if (!$headerFound) {
                $joined = implode(' ', $normalizedRow);
                if (str_contains($joined, 'laporan') || str_contains($joined, 'template') || str_contains($joined, 'dicetak') || str_contains($joined, 'petunjuk') || trim($joined) === '') {
                    continue; // Lewati judul/keterangan
                }

                // Jika format 4 kolom tanpa judul di baris 1
                if (count(array_filter($values)) >= 3 && !$hasNamaHeader) {
                    if (count($values) === 4) {
                        $colNama = 0; $colMapel = 1; $colKode = 2; $colDeskripsi = 3;
                    }
                }
            }

            // Ambil isi cell
            $nama      = trim((string)($values[$colNama] ?? ''));
            $mapel     = trim((string)($values[$colMapel] ?? ''));
            $kode      = strtoupper(trim((string)($values[$colKode] ?? '')));
            $deskripsi = trim((string)($values[$colDeskripsi] ?? ''));

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
                $errors[] = "Baris {$physicalRow} (kolom " . implode(', ', $missingFields) . " belum tuntas diisi)";
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
                    $errors[] = "Baris {$physicalRow} (kode kelas '{$kode}' sudah ada di sistem tetapi datanya berbeda)";
                    continue;
                } else {
                    // Kode sama DAN isi sama persis -> dilewati (tanpa error)
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
                    $errors[] = "Baris {$physicalRow} (kode kelas '{$kode}' sama dengan baris {$prev['row']} tetapi isinya berbeda)";
                    continue;
                } else {
                    continue;
                }
            }

            // Catat baris data baru
            $seenInFile[$kode] = [
                'row'            => $physicalRow,
                'nama_kelas'     => $nama,
                'mata_pelajaran' => $mapel,
                'deskripsi'      => $deskripsi,
            ];

            $validData[] = [
                'nama_kelas'     => $nama,
                'mata_pelajaran' => $mapel,
                'kode_kelas'     => $kode,
                'guru_id'        => auth()->id() ?? 1,
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
