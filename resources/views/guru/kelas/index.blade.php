<!-- ─── TAB KELOLA KELAS GURU ─── -->
<div id="tab-kelas" class="tab-content">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="font-display text-2xl text-[#241508]">Kelola Kelas</h1>
            <p class="text-sm text-[#6E4A2E] mt-0.5">Daftar kelas yang Anda ampuh.</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Search -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-[#8B6340]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="search-kelas-input" placeholder="Cari kelas..." class="rounded-lg pl-9 pr-3 py-2 text-xs text-[#241508] placeholder-[#8B6340] focus:outline-none w-40" style="background:#F3EDE2; border:1px solid #050404;" />
            </div>
            <!-- Tombol Ekspor PDF Kelas -->
            <a href="{{ route('laporan.kelas.pdf') }}" title="Unduh Laporan Kelas PDF" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-colors cursor-pointer text-white bg-[#F5576C] hover:bg-[#E0455B] shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Ekspor PDF
            </a>
            <!-- Tombol Ekspor Excel Kelas -->
            <a href="{{ route('laporan.kelas.excel') }}" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-colors cursor-pointer text-white shadow-sm" style="background:#1a7a3e;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Ekspor Excel
            </a>
            <!-- Tombol Buka Modal Impor Excel -->
            <button type="button" onclick="openImportKelasModal()" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-colors cursor-pointer text-[#4A2E1A] bg-[#E8F5E9] border border-[#A5D6A7] hover:bg-[#C8E6C9] shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#2E7D32]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Impor Excel
            </button>
            <!-- Tombol Tambah Kelas (Resource route: kelas.create) -->
            <a href="{{ route('kelas.create') }}" class="flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer" style="background:#4A2E1A; color:#FAF8F4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Kelas
            </a>
        </div>
    </div>

    <!-- Daftar Kelas -->
    <div class="space-y-2" id="kelas-list-container">
        @if(isset($kelasList) && $kelasList->count() > 0)
            @foreach($kelasList as $kelas)
                <div class="data-card kelas-item-card rounded-xl px-5 py-4"
                    style="background:#F3EDE2; border:1px solid #D4C0A0;"
                    data-nama="{{ strtolower($kelas->nama_kelas) }}"
                    data-mapel="{{ strtolower($kelas->mata_pelajaran) }}"
                    data-kode="{{ strtolower($kelas->kode_kelas) }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-semibold text-[#241508] text-sm">{{ $kelas->nama_kelas }}</h3>
                                <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-md" style="background:#4A2E1A; color:#FAF8F4;">{{ $kelas->kode_kelas }}</span>
                                <span class="text-[11px] text-[#6E4A2E] font-medium">{{ $kelas->mata_pelajaran }}</span>
                            </div>
                            <p class="text-[11px] text-[#8B6340] flex flex-wrap items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                                    {{ $kelas->materi->count() }} materi
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/></svg>
                                    {{ $kelas->kuis->count() }} kuis
                                </span>
                                @if($kelas->deskripsi)
                                    <span class="italic truncate max-w-xs">{{ $kelas->deskripsi }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Resource route: kelas.show -->
                            <a href="{{ route('kelas.show', $kelas->id) }}" class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors cursor-pointer" style="color:#4A2E1A; background:#EDE5D8; border:1px solid #C4A882;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Detail
                            </a>
                            <!-- Resource route: kelas.edit -->
                            <a href="{{ route('kelas.edit', $kelas->id) }}" class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors cursor-pointer" style="color:#4A2E1A; background:#EDE5D8; border:1px solid #C4A882;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <!-- Resource route: kelas.destroy -->
                            <form action="{{ route('kelas.destroy', $kelas->id) }}" method="POST" onsubmit="return confirm('Hapus kelas {{ $kelas->nama_kelas }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors cursor-pointer" style="color:#c62828; background:#fce4ec; border:1px solid #ef9a9a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="rounded-xl px-6 py-12 text-center" style="background:#F3EDE2; border:2px dashed #D4C0A0;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color:#C4A882;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                <p class="text-sm font-semibold text-[#4A2E1A] mb-1">Belum ada kelas</p>
                <p class="text-xs text-[#8B6340] mb-4">Buat kelas pertama Anda sekarang.</p>
                <a href="{{ route('kelas.create') }}" class="inline-flex items-center gap-2 text-xs font-semibold px-5 py-2.5 rounded-lg transition-colors" style="background:#4A2E1A; color:#FAF8F4;">
                    + Tambah Kelas Sekarang
                </a>
            </div>
        @endif
    </div>
    <div class=footer style="text-align:center; margin-top:20px; color:#6E4A2E; font-size:12px;">
        <p>E-Learning by kelompok 2.</p>
    </div>
</div>

<!-- Modal Impor Data Kelas dari Excel -->
<div id="modal-import-kelas" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 transition-all">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-[#D4C0A0]">
        <div class="flex items-center justify-between pb-3 border-b border-[#F3EDE2]">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-[#E8F5E9] text-[#2E7D32]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-lg text-[#241508]">Impor Data Kelas</h3>
                    <p class="text-xs text-[#8B6340]">Unggah file Excel (.xlsx / .csv)</p>
                </div>
            </div>
            <button type="button" onclick="closeImportKelasModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#4A2E1A] mb-1.5">Pilih File Template Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#1a7a3e] file:text-white hover:file:bg-[#15652f] border border-[#D4C0A0] rounded-lg cursor-pointer bg-[#FAF8F4]" />
                @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg bg-[#FAF8F4] border border-[#D4C0A0] p-3 text-xs text-[#6E4A2E] space-y-1">
                <p class="font-semibold text-[#241508]">Petunjuk Pengisian:</p>
                <p>1. Unduh template resmi melalui tombol di bawah.</p>
                <p>2. Isi data sesuai kolom (nama_kelas, mata_pelajaran, kode_kelas, deskripsi).</p>
                <p>3. Simpan dan unggah kembali file tersebut di sini.</p>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('kelas.template') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1a7a3e] hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Unduh Template Excel
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeImportKelasModal()" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-gray-800">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white rounded-lg shadow-sm" style="background:#1a7a3e;">
                        Mulai Impor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openImportKelasModal() {
    const modal = document.getElementById('modal-import-kelas');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeImportKelasModal() {
    const modal = document.getElementById('modal-import-kelas');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.getElementById('search-kelas-input')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    document.querySelectorAll('.kelas-item-card').forEach(card => {
        const match = ['data-nama','data-mapel','data-kode'].some(a => (card.getAttribute(a)||'').includes(term));
        card.style.display = match ? '' : 'none';
    });
});
</script>

