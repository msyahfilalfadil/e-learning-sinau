<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Nilai Siswa - SINAU</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #241508;
            line-height: 1.4;
            margin: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #241508;
            margin-bottom: 16px;
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
            margin-bottom: 16px;
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

        /* Badge Status & Grade */
        .badge-lulus {
            color: #1b5e20;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-remedial {
            color: #b71c1c;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-grade {
            display: inline-block;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 3px;
            background: #EFE6D8;
            color: #4A2E1A;
            border: 1px solid #D4C0A0;
        }

        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 24px;
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
                <div class="kop-subtitle">Biro Evaluasi Hasil Belajar & Penilaian Akademik Peserta Didik</div>
                <div class="kop-address">Kampus Digital Terintegrasi · Sistem Informasi Berbasis Web · sinau.test</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div style="font-size: 9px; color: #8B6340;">
                    Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }} WIB</strong><br>
                    Status Dokumen: <strong>Rekap Nilai Resmi</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Header Dokumen -->
    <div class="report-header">
        <h2>Laporan Rekapitulasi Nilai & Evaluasi Kuis Siswa</h2>
        <p>Daftar perolehan nilai, grade kelulusan, dan status evaluasi kuis secara komprehensif</p>
    </div>

    <!-- Tabel Data Rekap Nilai -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 18%;">Nama Siswa</th>
                <th style="width: 16%;">Email / Akun</th>
                <th style="width: 16%;">Kelas / Mapel</th>
                <th style="width: 18%;">Judul Kuis</th>
                <th style="width: 6%;">Passing</th>
                <th style="width: 6%;">Nilai</th>
                <th style="width: 6%;">Grade</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($nilaiList as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="font-bold">{{ $item->siswa->name ?? 'Siswa #'.$item->siswa_id }}</td>
                <td>{{ $item->siswa->email ?? '-' }}</td>
                <td>{{ $item->kuis->kelas->nama_kelas ?? ($item->kuis->kelas->mata_pelajaran ?? '-') }}</td>
                <td>{{ $item->kuis->judul ?? 'Kuis #'.$item->kuis_id }}</td>
                <td class="text-center">{{ $item->kuis->passing_grade ?? 70 }}</td>
                <td class="text-center font-bold" style="font-size: 11px;">{{ $item->nilai }}</td>
                <td class="text-center">
                    <span class="badge-grade">{{ $item->grade ?? '-' }}</span>
                </td>
                <td class="text-center">
                    @if (strtolower($item->status) === 'lulus' || $item->nilai >= ($item->kuis->passing_grade ?? 70))
                        <span class="badge-lulus">LULUS</span>
                    @else
                        <span class="badge-remedial">REMEDIAL</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 16px; color: #8B6340;">
                    Belum ada riwayat pengerjaan kuis siswa yang tercatat di database.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if ($nilaiList->isNotEmpty())
        <tfoot>
            <tr style="background-color: #F3EDE2;">
                <td colspan="6" class="font-bold text-right">Rata-rata Nilai Keseluruhan:</td>
                <td class="font-bold text-center">{{ number_format($nilaiList->avg('nilai'), 1) }}</td>
                <td colspan="2" class="font-bold text-center">Total: {{ $nilaiList->count() }} Data</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Tanda Tangan & Pengesahan -->
    <table class="ttd-table">
        <tr>
            <td style="width: 65%;">
                <p style="margin: 0; font-size: 9px; color: #8B6340;">
                    <em>Catatan Keabsahan:</em><br>
                    • Rekapitulasi ini dihasilkan secara otomatis dari sistem pengerjaan kuis online SINAU.<br>
                    • Nilai yang dinyatakan REMEDIAL berhak mengikuti ujian perbaikan sesuai kebijakan guru pengampu.
                </p>
            </td>
            <td style="width: 35%; text-align: center;">
                <p style="margin: 0 0 50px 0;">
                    Semarang, {{ date('d F Y') }}<br>
                    <strong>Guru Pengampu / Penilai</strong>
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

