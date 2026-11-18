<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'guru_id',
        'nama_kelas',
        'mata_pelajaran',
        'kode_kelas',
        'deskripsi',
    ];

    /**
     * Menghasilkan Barcode 1D (Code 39) dalam bentuk HTML.
     * $width: ketebalan garis (default: 2)
     * $height: tinggi garis dalam px (default: 50)
     */
    public function barcodeHtml(int $width = 2, int $height = 50): string
    {
        return DNS1D::getBarcodeHTML($this->kode_kelas, 'C39', $width, $height);
    }

    /**
     * Menghasilkan QR Code 2D dalam bentuk HTML.
     * $width: ukuran blok horizontal (default: 4)
     * $height: ukuran blok vertikal (default: 4)
     */
    public function qrcodeHtml(int $width = 4, int $height = 4): string
    {
        return DNS2D::getBarcodeHTML($this->kode_kelas, 'QRCODE', $width, $height);
    }

    /**
     * Relasi ke Guru (User pengampu).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relasi ke Materi
     */
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kelas_id');
    }

    /**
     * Relasi ke Kuis
     */
    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'kelas_id');
    }
}
