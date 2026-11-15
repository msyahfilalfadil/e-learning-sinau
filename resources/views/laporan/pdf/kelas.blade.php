<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Data Kelas - SINAU</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #241508;
            line-height: 1.4;
            margin: 20px 25px;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #241508;
            margin-bottom: 18px;
            padding-bottom: 8px;
        }
        .kop-table td {
            border: none;
            vertical-align: middle;
        }
        .kop-title {
            font-size: 18px;
            font-weight: bold;
            color: #4A2E1A;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .kop-subtitle {
            font-size: 11px;
            color: #6E4A2E;
            margin-top: 2px;
        }
        .kop-address {
            font-size: 9px;
            color: #8B6340;
            margin-top: 2px;
        }

        /* Judul Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .report-header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            color: #241508;
        }
        .report-header p {
            font-size: 10px;
            color: #6E4A2E;
            margin: 0;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, 
        table.data-table td {
            border: 1px solid #7A5B3E;
            padding: 6px 8px;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #F3EDE2;
            color: #241508;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #FAF8F4;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Badge Kode Kelas */
        .badge-code {
            display: inline-block;
            padding: 2px 5px;
            background-color: #EFE6D8;
            border: 1px solid #D4C0A0;
            font-family: monospace;
            font-size: 9px;
            font-weight: bold;
            color: #4A2E1A;
            border-radius: 3px;
        }

        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            border: none;
        }
        .ttd-table td {
            border: none;
            vertical-align: top;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Formal -->
    <table class="kop-table">
        <tr>
            <td style="width: 70%;">
                <div class="kop-title">SINAU · E-Learning Platform</div>
                <div class="kop-subtitle">Sistem Informasi Manajemen Kelas & Pembelajaran Daring</div>
                <div class="kop-address">Pusat Layanan Edukasi Terpadu · Website: sinau.test · Dokumen Resmi Terverifikasi</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div style="font-size: 9px; color: #8B6340;">
                    Dicetak Pada: <strong>{{ date('d/m/Y H:i') }} WIB</strong><br>
                    Format Dokumen: <strong>Arsip Digital (PDF)</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Header Dokumen -->
    <div class="report-header">
        <h2>Laporan Data & Inventaris Kelas Aktif</h2>
        <p>Ringkasan beban materi dan kuis per kelas yang terdaftar dalam sistem</p>
    </div>

    <!-- Tabel Data Kelas -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 25%;">Nama Kelas</th>
                <th style="width: 20%;">Mata Pelajaran</th>
                <th style="width: 14%;">Kode Kelas</th>
                <th style="width: 21%;">Guru Pengampu</th>
                <th style="width: 8%;">Materi</th>
                <th style="width: 8%;">Kuis</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelasList as $kelas)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="font-bold">{{ $kelas->nama_kelas }}</td>
                <td>{{ $kelas->mata_pelajaran ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge-code">{{ $kelas->kode_kelas }}</span>
                </td>
                <td>{{ $kelas->guru->name ?? 'Belum Ditentukan' }}</td>
                <td class="text-center">{{ $kelas->materi->count() }}</td>
                <td class="text-center">{{ $kelas->kuis->count() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 16px; color: #8B6340;">
                    Belum ada data kelas yang terdaftar.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F3EDE2;">
                <td colspan="5" class="font-bold text-right">Total Keseluruhan Kelas:</td>
                <td colspan="2" class="font-bold text-center">{{ $kelasList->count() }} Kelas</td>
            </tr>
        </tfoot>
    </table>

    <!-- Area Tanda Tangan & Pengesahan -->
    <table class="ttd-table">
        <tr>
            <td style="width: 60%;">
                <p style="margin: 0; font-size: 9px; color: #8B6340;">
                    <em>Catatan:</em><br>
                    Dokumen ini digenerate secara otomatis melalui sistem SINAU E-Learning.<br>
                    Semua data yang tercantum merupakan data real-time pada saat laporan diunduh.
                </p>
            </td>
            <td style="width: 40%; text-align: center;">
                <p style="margin: 0 0 50px 0;">
                    Semarang, {{ date('d F Y') }}<br>
                    <strong>Koordinator Kurikulum & Pembelajaran</strong>
                </p>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    ( Bapak Hendra Kurnia )
                </p>
                <p style="margin: 2px 0 0 0; font-size: 9px; color: #6E4A2E;">
                    NIP. 19820412 200801 1 009
                </p>
            </td>
        </tr>
    </table>

</body>
</html>

