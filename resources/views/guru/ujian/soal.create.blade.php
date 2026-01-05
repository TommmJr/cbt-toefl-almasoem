<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal - {{ $section->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('guru.ujian.show', $section->ujian_id) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2">
                    <i data-lucide="arrow-left" size="16"></i> Kembali ke Detail Ujian
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Soal Baru</h1>
                <p class="text-gray-500 text-sm">Menambahkan soal ke section: <span class="font-semibold text-blue-600">{{ $section->judul }}</span></p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 max-w-4xl">
                
                <form action="{{ route('guru.ujian.soal.store', $section->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Pertanyaan --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Pertanyaan</label>
                        <input id="pertanyaan" type="hidden" name="pertanyaan" value="{{ old('pertanyaan') }}">
                        <trix-editor input="pertanyaan" class="trix-content min-h-[150px] rounded-lg border-gray-300"></trix-editor>
                        @error('pertanyaan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilihan Ganda Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 uppercase">Pilihan {{ $opt }}</label>
                            <div class="flex gap-2">
                                <span class="flex items-center justify-center w-8 h-10 bg-gray-100 border border-gray-300 rounded font-bold uppercase text-gray-500">{{ $opt }}</span>
                                <input type="text" name="pilihan_{{ $opt }}" value="{{ old('pilihan_'.$opt) }}"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Kunci Jawaban & Bobot --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-blue-50 p-6 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Kunci Jawaban</label>
                            <select name="kunci_jawaban" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white focus:ring-blue-500 outline-none" required>
                                <option value="" disabled selected>Pilih Kunci Jawaban...</option>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                                <option value="e">E</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Bobot Nilai</label>
                            <input type="number" name="bobot" value="5" min="1" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-blue-500 outline-none" required>
                            <p class="text-xs text-gray-500 mt-1">Nilai jika siswa menjawab benar.</p>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('guru.ujian.show', $section->ujian_id) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-md">
                            Simpan Soal
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>