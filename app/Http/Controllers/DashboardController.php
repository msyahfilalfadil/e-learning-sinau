<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman login / pilih role.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman dashboard Admin.
     */
    public function admin()
    {
        return view('admin.dashboard');
    }

    /**
     * Menampilkan halaman dashboard Guru.
     */
    public function guru()
    {
        return view('guru.dashboard');
    }

    /**
     * Menampilkan halaman dashboard Siswa (data dinamis dari database).
     * Khusus untuk siswa yang sedang login (isolasi data antar siswa).
     * Admin juga dapat melihat tampilan ruang siswa.
     */
    public function siswa()
    {
        $user = auth()->user();

        // Jika Admin yang melihat, muat data sampel siswa agar dashboard terisi lengkap
        if ($user && $user->role === 'admin') {
            $sampleSiswa = User::where('role', 'siswa')->first();
            $siswaId = $sampleSiswa ? $sampleSiswa->id : $user->id;
        } else {
            $siswaId = auth()->id();
        }

        $kelasList = Kelas::with(['guru', 'materi', 'kuis'])->latest()->get();
        $materiList = Materi::with('kelas')->latest()->get();
        $kuisList = Kuis::with(['kelas', 'soal', 'nilai'])->latest()->get();
        
        // HANYA memuat nilai milik siswa yang bersangkutan
        $nilaiList = Nilai::with(['kuis.kelas'])->where('siswa_id', $siswaId)->latest()->get();

        return view('siswa.dashboard', compact('kelasList', 'materiList', 'kuisList', 'nilaiList', 'siswaId'));
    }
}
