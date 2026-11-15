<!-- ─── TAB REKAP NILAI GURU ─── -->
<div id="tab-nilai" class="tab-content">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl text-[#241508]">Rekap Nilai Siswa</h1>
            <p class="text-sm text-[#8B6340] mt-0.5">Pantau performa nilai seluruh siswa.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.nilai.preview') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#D4C0A0] text-[#4A2E1A] bg-[#FAF8F4] hover:bg-[#F3EDE2] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                Preview PDF
            </a>
            <a href="{{ route('laporan.nilai.pdf') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg text-white bg-[#F5576C] hover:bg-[#E0455B] shadow-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Unduh Rekap PDF
            </a>
            <!-- Tombol Ekspor Excel Nilai -->
            <a href="{{ route('laporan.nilai.excel') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg text-white shadow-sm transition-colors" style="background:#1a7a3e;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Unduh Rekap Excel
            </a>
        </div>
    </div>
    <div class="bg-white border border-[#E6D9C6] rounded-xl overflow-hidden">
        <!-- Header Tabel -->
        <div class="grid grid-cols-12 px-5 py-3 bg-[#FAF8F4] border-b border-[#F3EDE2]">
            <span class="col-span-4 text-[10px] font-semibold uppercase tracking-wider text-[#8B6340]">Siswa</span>
            <span class="col-span-2 text-[10px] font-semibold uppercase tracking-wider text-[#8B6340] text-center">Kuis</span>
            <span class="col-span-3 text-[10px] font-semibold uppercase tracking-wider text-[#8B6340] text-center">Rata-rata</span>
            <span class="col-span-1 text-[10px] font-semibold uppercase tracking-wider text-[#8B6340] text-center">Grade</span>
            <span class="col-span-2 text-[10px] font-semibold uppercase tracking-wider text-[#8B6340] text-right">Edit</span>
        </div>

        @php
        $siswaData = [
            ['init' => 'AK', 'nama' => 'Arini Kusuma', 'kuis' => '3/3', 'skor' => 92, 'grade' => 'A'],
            ['init' => 'BP', 'nama' => 'Bagas Pratama', 'kuis' => '2/3', 'skor' => 75, 'grade' => 'B'],
            ['init' => 'CM', 'nama' => 'Citra Maharani', 'kuis' => '3/3', 'skor' => 88, 'grade' => 'A'],
        ];
        @endphp

        @foreach($siswaData as $idx => $s)
            <div class="grid grid-cols-12 px-5 py-4 items-center {{ $idx < count($siswaData)-1 ? 'border-b border-[#FAF8F4]' : '' }} hover:bg-[#FAF8F4] transition-colors">
                <div class="col-span-4 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#D4C0A0] flex items-center justify-center text-[#4A2E1A] text-[9px] font-semibold shrink-0">{{ $s['init'] }}</div>
                    <p class="text-sm font-medium text-[#241508]">{{ $s['nama'] }}</p>
                </div>
                <div class="col-span-2 text-center text-[11px] text-[#8B6340]">{{ $s['kuis'] }}</div>
                <div class="col-span-3 flex flex-col items-center gap-1.5">
                    <span class="text-sm font-bold text-[#241508]">{{ $s['skor'] }}</span>
                    <div class="w-16 progress-bar">
                        <div class="progress-fill" style="width:{{ $s['skor'] }}%"></div>
                    </div>
                </div>
                <div class="col-span-1 flex justify-center">
                    <span class="badge-grade badge-{{ strtolower($s['grade']) }}">{{ $s['grade'] }}</span>
                </div>
                <div class="col-span-2 flex justify-end">
                    <div class="flex items-center gap-2">
                        <input type="number" class="w-16 bg-[#FAF8F4] border border-[#E6D9C6] rounded-lg px-2 py-1 text-xs text-center focus:outline-none focus:border-[#8B6340]" placeholder="{{ $s['skor'] }}" />
                        <button onclick="simpanNilai(this.previousElementSibling)" class="text-[10px] font-semibold bg-[#4A2E1A] text-[#FAF8F4] px-2 py-1 rounded-lg">OK</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
