<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal - {{ $section->judul_section }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
    
    {{-- Trix Editor (Untuk Teks Kaya) --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <style>.trix-button-group--file-tools { display: none !important; } </style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8">
            
            <div class="mb-6">
                <a href="{{ route('guru.ujian.show', $section->ujian_id) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2">
                    <i data-lucide="arrow-left" size="16"></i> Kembali ke Detail Ujian
                </a>
                <h1 class="text-2xl font-bold text-gray-800">
                    Tambah Soal <span class="uppercase text-blue-600">{{ $section->tipe_section->value ?? $section->tipe_section }}</span>
                </h1>
                <p class="text-gray-500 text-sm">Section: <b>{{ $section->judul_section }}</b></p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 max-w-5xl">
                
                {{-- Form perlu enctype="multipart/form-data" buat upload file --}}
                <form action="{{ route('guru.ujian.soal.store', $section->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ========================================== --}}
                    {{-- 1. KHUSUS LISTENING (Upload Audio) --}}
                    {{-- ========================================== --}}
                    @if($section->tipe_section === 'listening' || (is_object($section->tipe_section) && $section->tipe_section->value === 'listening'))
                        <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                                <i data-lucide="headphones" size="18"></i> File Audio (MP3/WAV)
                            </label>
                            <input type="file" name="audio" accept="audio/*" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                            "/>
                            <p class="text-xs text-gray-500 mt-1">*Upload audio percakapan/monolog di sini.</p>
                        </div>
                    @endif

                    {{-- ========================================== --}}
                    {{-- 2. KHUSUS READING (Teks Bacaan) --}}
                    {{-- ========================================== --}}
                    @if($section->tipe_section === 'reading' || (is_object($section->tipe_section) && $section->tipe_section->value === 'reading'))
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-green-800 mb-2 flex items-center gap-2">
                                <i data-lucide="book-open" size="18"></i> Teks Bacaan (Passage)
                            </label>
                            <div class="text-xs text-gray-500 mb-1">Masukkan teks bacaan panjang di sini.</div>
                            <input id="passage" type="hidden" name="passage" value="{{ old('passage') }}">
                            <trix-editor input="passage" class="trix-content min-h-[150px] bg-green-50/30 border-green-200 rounded-lg"></trix-editor>
                        </div>
                    @endif

                    {{-- ========================================== --}}
                    {{-- 3. PERTANYAAN (Semua Tipe Butuh Ini) --}}
                    {{-- ========================================== --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Pertanyaan / Instruksi Soal</label>
                        <input id="pertanyaan" type="hidden" name="pertanyaan" value="{{ old('pertanyaan') }}">
                        <trix-editor input="pertanyaan" class="trix-content min-h-[100px] rounded-lg border-gray-300"></trix-editor>
                        @error('pertanyaan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ========================================== --}}
                    {{-- 4. PILIHAN GANDA (Hanya Listening, Reading, Structure) --}}
                    {{-- ========================================== --}}
                    @if($section->tipe_section !== 'writing' && (!is_object($section->tipe_section) || $section->tipe_section->value !== 'writing'))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            @foreach(['a', 'b', 'c', 'd'] as $opt)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 uppercase">Pilihan {{ $opt }}</label>
                                <div class="flex gap-2">
                                    <span class="flex items-center justify-center w-8 h-10 bg-gray-100 border border-gray-300 rounded font-bold uppercase text-gray-500">{{ $opt }}</span>
                                    <input type="text" name="pilihan_{{ $opt }}" value="{{ old('pilihan_'.$opt) }}"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 focus:ring-blue-500 focus:border-blue-500 outline-none" 
                                        placeholder="Jawaban opsi {{ strtoupper($opt) }}">
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Kunci Jawaban</label>
                                <select name="kunci_jawaban" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white focus:ring-blue-500 outline-none">
                                    <option value="" disabled selected>-- Pilih Kunci --</option>
                                    @foreach(['a', 'b', 'c', 'd'] as $key)
                                        <option value="{{ $key }}">{{ strtoupper($key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Bobot Nilai</label>
                                <input type="number" name="bobot" value="1" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 outline-none">
                            </div>
                        </div>

                    {{-- ========================================== --}}
                    {{-- 5. KHUSUS WRITING (Essay Settings) --}}
                    {{-- ========================================== --}}
                    @else
                        <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-200 mb-8">
                            <h3 class="font-bold text-yellow-800 mb-4 flex items-center gap-2">
                                <i data-lucide="pen-tool" size="18"></i> Pengaturan Essay (Writing)
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Minimal Kata</label>
                                    <input type="number" name="min_kata" value="150" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <p class="text-xs text-gray-500">Target minimal kata yang harus ditulis siswa.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-1">Maksimal Kata (Opsional)</label>
                                    <input type="number" name="max_kata" value="300" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                </div>
                            </div>
                            {{-- Hidden fields biar controller gak error validasi --}}
                            <input type="hidden" name="pilihan_a" value="-">
                            <input type="hidden" name="pilihan_b" value="-">
                            <input type="hidden" name="pilihan_c" value="-">
                            <input type="hidden" name="pilihan_d" value="-">
                            <input type="hidden" name="kunci_jawaban" value="essay">
                        </div>
                    @endif

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('guru.ujian.show', $section->ujian_id) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-md">Simpan Soal</button>
                    </div>

                </form>
            </div>

        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>