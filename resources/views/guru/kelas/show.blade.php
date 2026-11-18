@extends('layouts.app')

@section('title', 'SINAU — Detail Kelas: ' . $kelas->nama_kelas)

@section('content')
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar Guru -->
    @include('partials.sidebar-guru')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto" style="background: #EDE5D8;">
        <div class="max-w-3xl mx-auto px-7 py-7">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs text-[#8B6340] mb-4">
                <a href="{{ route('kelas.index') }}" class="hover:underline">Kelola Kelas</a>
                <span>/</span>
                <span class="text-[#241508] font-semibold">Detail Kelas</span>
            </div>

            <!-- Card Detail Kelas -->
            <div class="rounded-2xl shadow-xl overflow-hidden border border-[#D4C0A0]" style="background:#FAF8F4;">
                <!-- Header Card -->
                <div class="flex items-center justify-between px-6 py-5" style="background:#4A2E1A;">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="font-display text-xl text-[#FAF8F4]">{{ $kelas->nama_kelas }}</h1>
                            <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-md" style="background:#6E4A2E; color:#FAF8F4;">
                                {{ $kelas->kode_kelas }}
                            </span>
                        </div>
                        <p class="text-xs text-[#D4C0A0] mt-1">{{ $kelas->mata_pelajaran }} &bull; Pengampu: {{ $kelas->guru->name ?? 'Bapak Hendra Kurnia' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('kelas.edit', $kelas->id) }}" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors" style="background:#EDE5D8; color:#4A2E1A;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Edit Kelas
                        </a>
                        <a href="{{ route('kelas.index') }}" class="text-xs text-[#D4C0A0] hover:text-[#FAF8F4] px-3 py-1.5 rounded-lg transition-colors" style="background:rgba(255,255,255,0.1);">
                            &larr; Kembali
                        </a>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Section: Barcode & QR Code Identitas Kelas (Tugas Integrasi Barcode Milon) -->
                    <div class="rounded-2xl p-5 border border-[#D4C0A0]" style="background:#FFF8F0;">
                        <div class="flex items-center justify-between pb-3 border-b border-[#E6D9C6] mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-[#4A2E1A] text-[#FAF8F4]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 5v14"/><path d="M8 5v14"/><path d="M12 5v14"/><path d="M17 5v14"/><path d="M21 5v14"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-sm font-bold text-[#241508]">Barcode & QR Code Identitas Kelas</h3>
                                    <p class="text-[11px] text-[#8B6340]">Dihasilkan otomatis dari kode kelas: <span class="font-mono font-bold text-[#4A2E1A]">{{ $kelas->kode_kelas }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 bg-[#EDE5D8] p-1 rounded-lg">
                                <button type="button" onclick="switchBarcodeView('c39')" id="btn-tab-c39" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-all bg-[#4A2E1A] text-[#FAF8F4] shadow-sm">
                                    Barcode 1D (Code 39)
                                </button>
                                <button type="button" onclick="switchBarcodeView('qrcode')" id="btn-tab-qrcode" class="px-2.5 py-1 text-xs font-semibold rounded-md transition-all text-[#6E4A2E] hover:text-[#241508]">
                                    QR Code (2D)
                                </button>
                            </div>
                        </div>

                        <!-- 1. Tampilan Barcode 1D (Code 39) -->
                        <div id="barcode-c39-container" class="space-y-4">
                            <!-- Pilihan Ukuran Barcode (Memenuhi Tugas: Coba ganti size dari Barcode) -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-[#6E4A2E] font-medium">Pilih Ukuran Barcode:</span>
                                <div class="inline-flex rounded-lg border border-[#D4C0A0] bg-[#FAF8F4] p-0.5 text-xs">
                                    <button type="button" onclick="setBarcodeSize('small')" id="size-btn-small" class="px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]">Kecil (1×40)</button>
                                    <button type="button" onclick="setBarcodeSize('medium')" id="size-btn-medium" class="px-2.5 py-1 rounded-md bg-[#4A2E1A] text-[#FAF8F4] font-semibold">Sedang (2×60)</button>
                                    <button type="button" onclick="setBarcodeSize('large')" id="size-btn-large" class="px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]">Besar (3×80)</button>
                                </div>
                            </div>

                            <!-- Area Render Barcode -->
                            <div class="bg-white rounded-xl border border-[#E6D9C6] p-6 flex flex-col items-center justify-center shadow-inner overflow-x-auto min-h-[140px]">
                                <!-- Ukuran Kecil -->
                                <div id="barcode-size-small" class="barcode-size-item hidden flex-col items-center">
                                    <div class="bg-white p-2">
                                        {!! $kelas->barcodeHtml(1, 40) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-xs tracking-widest text-[#241508] font-bold">* {{ $kelas->kode_kelas }} *</p>
                                    <span class="text-[10px] text-gray-400 mt-1">Format: Code 39 · Ukuran Kecil (w:1, h:40px)</span>
                                </div>

                                <!-- Ukuran Sedang (Default) -->
                                <div id="barcode-size-medium" class="barcode-size-item flex flex-col items-center">
                                    <div class="bg-white p-2">
                                        {!! $kelas->barcodeHtml(2, 60) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-sm tracking-widest text-[#241508] font-bold">* {{ $kelas->kode_kelas }} *</p>
                                    <span class="text-[10px] text-gray-400 mt-1">Format: Code 39 · Ukuran Standar (w:2, h:60px)</span>
                                </div>

                                <!-- Ukuran Besar -->
                                <div id="barcode-size-large" class="barcode-size-item hidden flex-col items-center">
                                    <div class="bg-white p-2">
                                        {!! $kelas->barcodeHtml(3, 80) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-base tracking-widest text-[#241508] font-bold">* {{ $kelas->kode_kelas }} *</p>
                                    <span class="text-[10px] text-gray-400 mt-1">Format: Code 39 · Ukuran Ekstra Besar (w:3, h:80px)</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Tampilan QR Code 2D (Memenuhi Tugas: Coba Format QR code) -->
                        <div id="barcode-qrcode-container" class="hidden space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-[#6E4A2E] font-medium">Pilihan Ukuran QR Code:</span>
                                <div class="inline-flex rounded-lg border border-[#D4C0A0] bg-[#FAF8F4] p-0.5 text-xs">
                                    <button type="button" onclick="setQrSize('qr-small')" id="btn-qr-small" class="px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]">Kecil (3×3)</button>
                                    <button type="button" onclick="setQrSize('qr-medium')" id="btn-qr-medium" class="px-2.5 py-1 rounded-md bg-[#4A2E1A] text-[#FAF8F4] font-semibold">Sedang (5×5)</button>
                                    <button type="button" onclick="setQrSize('qr-large')" id="btn-qr-large" class="px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]">Besar (7×7)</button>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl border border-[#E6D9C6] p-6 flex flex-col items-center justify-center shadow-inner min-h-[160px]">
                                <!-- QR Kecil -->
                                <div id="box-qr-small" class="qr-size-item hidden flex-col items-center">
                                    <div class="p-2 bg-white border border-gray-100 rounded-lg shadow-sm">
                                        {!! $kelas->qrcodeHtml(3, 3) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-xs text-[#241508] font-bold">{{ $kelas->kode_kelas }}</p>
                                    <span class="text-[10px] text-gray-400">QR Code (3×3)</span>
                                </div>

                                <!-- QR Sedang (Default) -->
                                <div id="box-qr-medium" class="qr-size-item flex flex-col items-center">
                                    <div class="p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                                        {!! $kelas->qrcodeHtml(5, 5) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-sm text-[#241508] font-bold">{{ $kelas->kode_kelas }}</p>
                                    <span class="text-[10px] text-gray-400">QR Code (5×5) · Siap Pindai Kamera HP Siswa</span>
                                </div>

                                <!-- QR Besar -->
                                <div id="box-qr-large" class="qr-size-item hidden flex-col items-center">
                                    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                                        {!! $kelas->qrcodeHtml(7, 7) !!}
                                    </div>
                                    <p class="mt-2 font-mono text-base text-[#241508] font-bold">{{ $kelas->kode_kelas }}</p>
                                    <span class="text-[10px] text-gray-400">QR Code (7×7) · Tampilan Layar Proyektor Kelas</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info & Tombol Cetak -->
                        <div class="mt-4 pt-3 border-t border-[#E6D9C6] flex items-center justify-between text-xs text-[#6E4A2E]">
                            <div class="flex items-center gap-1.5 text-[11px]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#1a7a3e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <span>Dirender langsung melalui <strong>milon/barcode</strong> (HTML CSS murni tanpa simpan file).</span>
                            </div>
                            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[#D4C0A0] bg-[#FAF8F4] text-xs font-semibold text-[#4A2E1A] hover:bg-[#EDE5D8]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                                </svg>
                                Cetak Kartu Barcode
                            </button>
                        </div>
                    </div>

                    <!-- Deskripsi Kelas -->
                    @if($kelas->deskripsi)
                        <div class="rounded-xl p-4 border border-[#E6D9C6]" style="background:#F3EDE2;">
                            <p class="text-xs font-semibold text-[#4A2E1A] uppercase tracking-wider mb-1">Deskripsi / Jadwal Pembelajaran</p>
                            <p class="text-xs text-[#6E4A2E] leading-relaxed">{{ $kelas->deskripsi }}</p>
                        </div>
                    @endif

                    <!-- Modul Materi di Kelas Ini -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-xs font-semibold text-[#4A2E1A] uppercase tracking-wider flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#8B6340]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                Modul Materi ({{ $kelas->materi->count() }})
                            </h2>
                            <a href="{{ route('materi.create') }}" class="text-[11px] font-semibold text-[#4A2E1A] hover:underline">+ Unggah Materi</a>
                        </div>
                        @if($kelas->materi->count() > 0)
                            <div class="space-y-2">
                                @foreach($kelas->materi as $m)
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-[#E6D9C6]" style="background:#FFF;">
                                        <div>
                                            <p class="text-xs font-semibold text-[#241508]">{{ $m->judul }}</p>
                                            @if($m->deskripsi)<p class="text-[11px] text-[#8B6340]">{{ $m->deskripsi }}</p>@endif
                                        </div>
                                        @if($m->file_path)
                                            <a href="{{ route('materi.download', $m->id) }}" class="text-xs font-semibold px-2.5 py-1 rounded" style="background:#EDE5D8; color:#4A2E1A;">
                                                Unduh File
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[#8B6340] italic py-2">Belum ada modul materi di kelas ini.</p>
                        @endif
                    </div>

                    <!-- Kuis & Evaluasi di Kelas Ini -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-xs font-semibold text-[#4A2E1A] uppercase tracking-wider flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#8B6340]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Kuis & Evaluasi ({{ $kelas->kuis->count() }})
                            </h2>
                            <a href="{{ route('kuis.create') }}" class="text-[11px] font-semibold text-[#4A2E1A] hover:underline">+ Buat Kuis</a>
                        </div>
                        @if($kelas->kuis->count() > 0)
                            <div class="space-y-2">
                                @foreach($kelas->kuis as $q)
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-[#E6D9C6]" style="background:#FFF;">
                                        <div>
                                            <p class="text-xs font-semibold text-[#241508]">{{ $q->judul }}</p>
                                            <p class="text-[11px] text-[#8B6340]">Durasi: {{ $q->durasi_menit }} menit &bull; KKM: {{ $q->passing_grade }} &bull; {{ $q->soal->count() }} butir soal</p>
                                        </div>
                                        <a href="{{ route('kuis.show', $q->id) }}" class="text-xs font-semibold px-2.5 py-1 rounded" style="background:#EDE5D8; color:#4A2E1A;">
                                            Lihat Soal
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[#8B6340] italic py-2">Belum ada kuis di kelas ini.</p>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="px-6 py-4 border-t border-[#E6D9C6] flex justify-end">
                    <a href="{{ route('kelas.index') }}" class="text-xs font-semibold px-4 py-2 rounded-lg" style="background:#4A2E1A; color:#FAF8F4;">
                        Kembali ke Kelola Kelas
                    </a>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    function switchBarcodeView(type) {
        const c39Container = document.getElementById('barcode-c39-container');
        const qrContainer = document.getElementById('barcode-qrcode-container');
        const btnC39 = document.getElementById('btn-tab-c39');
        const btnQr = document.getElementById('btn-tab-qrcode');

        if (type === 'c39') {
            c39Container.classList.remove('hidden');
            qrContainer.classList.add('hidden');
            btnC39.className = "px-2.5 py-1 text-xs font-semibold rounded-md transition-all bg-[#4A2E1A] text-[#FAF8F4] shadow-sm";
            btnQr.className = "px-2.5 py-1 text-xs font-semibold rounded-md transition-all text-[#6E4A2E] hover:text-[#241508]";
        } else {
            c39Container.classList.add('hidden');
            qrContainer.classList.remove('hidden');
            btnQr.className = "px-2.5 py-1 text-xs font-semibold rounded-md transition-all bg-[#4A2E1A] text-[#FAF8F4] shadow-sm";
            btnC39.className = "px-2.5 py-1 text-xs font-semibold rounded-md transition-all text-[#6E4A2E] hover:text-[#241508]";
        }
    }

    function setBarcodeSize(size) {
        document.querySelectorAll('.barcode-size-item').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        });

        const target = document.getElementById('barcode-size-' + size);
        if (target) {
            target.classList.remove('hidden');
            target.classList.add('flex');
        }

        ['small', 'medium', 'large'].forEach(s => {
            const btn = document.getElementById('size-btn-' + s);
            if (!btn) return;
            if (s === size) {
                btn.className = "px-2.5 py-1 rounded-md bg-[#4A2E1A] text-[#FAF8F4] font-semibold";
            } else {
                btn.className = "px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]";
            }
        });
    }

    function setQrSize(size) {
        document.querySelectorAll('.qr-size-item').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        });

        const target = document.getElementById('box-' + size);
        if (target) {
            target.classList.remove('hidden');
            target.classList.add('flex');
        }

        ['qr-small', 'qr-medium', 'qr-large'].forEach(s => {
            const btn = document.getElementById('btn-' + s);
            if (!btn) return;
            if (s === size) {
                btn.className = "px-2.5 py-1 rounded-md bg-[#4A2E1A] text-[#FAF8F4] font-semibold";
            } else {
                btn.className = "px-2.5 py-1 rounded-md text-[#6E4A2E] hover:bg-[#EDE5D8]";
            }
        });
    }
</script>
@endsection
