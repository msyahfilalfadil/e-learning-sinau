<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes — SINAU
|--------------------------------------------------------------------------
*/

// Auth Routes (Publik: Login, Quick Role Login, Logout)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login-as/{role}', [AuthController::class, 'loginAs'])->name('login.as');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --------------------------------------------------------------------------

// 1. AREA ADMINISTRATOR (Hanya Role Admin, Guru & Siswa DILARANG)
// --------------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
});

// --------------------------------------------------------------------------

// 2. AREA GURU (Hanya Role Guru, Siswa & Admin DILARANG)
// --------------------------------------------------------------------------
Route::middleware(['auth', 'role:guru'])->group(function () {
    // Dashboard Guru
    Route::get('/guru', [KelasController::class, 'index'])->name('guru');

    // Route Import & Template Kelas (Wajib didefinisikan sebelum resource agar tidak bentrok dengan {kelas})
    Route::get('/kelas/template', [KelasController::class, 'downloadTemplate'])->name('kelas.template');
    Route::post('/kelas/import', [KelasController::class, 'import'])->name('kelas.import');

    // Resource Routes Manajemen (PBL Standar)
    Route::resource('kelas', KelasController::class);
    Route::resource('materi', MateriController::class);
    Route::resource('kuis', KuisController::class);

    // Alias group untuk kompatibilitas route prefix 'guru.*'
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::resource('kelas', KelasController::class);
        Route::resource('materi', MateriController::class);
        Route::resource('kuis', KuisController::class);
        Route::post('/soal', [KuisController::class, 'storeSoal'])->name('soal.store');
        Route::delete('/soal/{id}', [KuisController::class, 'destroySoal'])->name('soal.destroy');
    });

    // Export Laporan PDF (barryvdh/laravel-dompdf)
    Route::get('/laporan/kelas/pdf', [LaporanController::class, 'exportPdfKelas'])->name('laporan.kelas.pdf');
    Route::get('/laporan/kelas/preview', [LaporanController::class, 'streamPdfKelas'])->name('laporan.kelas.preview');
    Route::get('/laporan/nilai/pdf', [LaporanController::class, 'exportPdfNilai'])->name('laporan.nilai.pdf');
    Route::get('/laporan/nilai/preview', [LaporanController::class, 'streamPdfNilai'])->name('laporan.nilai.preview');

    // Export Laporan Excel (maatwebsite/excel)
    Route::get('/laporan/kelas/excel', [LaporanController::class, 'exportExcelKelas'])->name('laporan.kelas.excel');
    Route::get('/laporan/nilai/excel', [LaporanController::class, 'exportExcelNilai'])->name('laporan.nilai.excel');
});

// --------------------------------------------------------------------------

// 3. AREA SISWA (Hanya Role Siswa, Guru & Admin DILARANG)  
// --------------------------------------------------------------------------

Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa', [DashboardController::class, 'siswa'])->name('siswa');
    Route::get('/materi/download/{id}', [MateriController::class, 'download'])->name('materi.download');
    Route::post('/siswa/kuis/{id}/submit', [KuisController::class, 'submitJawaban'])->name('siswa.kuis.submit');
});
