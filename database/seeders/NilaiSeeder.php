<?php

namespace Database\Seeders;

use App\Models\Kuis;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = User::where('role', 'siswa')->first() ?? User::first();
        if (!$siswa) {
            return;
        }

        $kuisList = Kuis::with('kelas')->get();

        $sampleData = [
            ['skor' => 95, 'grade' => 'A', 'status' => 'lulus'],
            ['skor' => 88, 'grade' => 'A', 'status' => 'lulus'],
            ['skor' => 65, 'grade' => 'C', 'status' => 'tidak_lulus'],
        ];

        foreach ($kuisList as $index => $kuis) {
            $data = $sampleData[$index % count($sampleData)];
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'kuis_id'  => $kuis->id,
                ],
                [
                    'nilai'  => $data['skor'],
                    'grade'  => $data['grade'],
                    'status' => $data['status'],
                ]
            );
        }
    }
}

