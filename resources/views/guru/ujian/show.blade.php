<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Ujian - {{ $ujian->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2 transition">
                    <i data-lucide="arrow-left" size="16"></i> Kembali ke Daftar
                </a>
                
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $ujian->judul }}</h1>
                        <div class="flex items-center gap-3 mt-2 text-sm">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-mono font-bold">{{ $ujian->kode_ujian }}</span>
                            <span class="text-gray-500 flex items-center gap-1">
                                <i data-lucide="clock" size="14"></i> Total: {{ $ujian->durasi_menit }} Menit
                            </span>
                        </div>
                    </div>
                    
                    {{-- Tombol Header --}}
                    <div class="flex gap-2">
                        @if(!$ujian->is_published)
                            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition">
                                <i data-lucide="upload-cloud" size="18"></i> Publish
                            </button>
                        @else
                            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg flex items-center gap-2 border border-green-200">
                                <i data-lucide="check-circle" size="18"></i> Published
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pesan Sukses --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- LOGIC: Cek apakah Section sudah ada? --}}
            @if($ujian->sections->count() > 0)
                
                {{-- KONTEN UTAMA: Jika Section Ada --}}
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">Daftar Section</h2>
                        <button onclick="document.getElementById('modalSection').classList.remove('hidden')" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 shadow-sm">
                            <i data-lucide="plus"></i> Tambah Section
                        </button>
                    </div>

                    {{-- Looping Section --}}
                    @foreach($ujian->sections as $section)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $section->judul }}</h3>
                                    <p class="text-xs text-gray-500">Durasi: {{ $section->durasi_menit }} Menit • {{ $section->soal->count() }} Soal</p>
                                </div>
                                <div class="flex gap-2">
                                   <a href="{{ route('guru.ujian.soal.create', $section->id) }}" 
                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded text-sm font-medium transition flex items-center gap-1">
                                        <i data-lucide="plus-circle" size="14"></i> Tambah Soal
                                    </a>
                                    <button class="text-gray-400 hover:text-red-600 px-2">
                                        <i data-lucide="trash-2" size="18"></i>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Preview Soal (Kosong dulu) --}}
                            <div class="p-6 text-center text-gray-400 text-sm italic">
                                Belum ada soal di section ini.
                            </div>
                        </div>
                    @endforeach
                </div>

            @else

                {{-- EMPTY STATE: Jika Belum Ada Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[400px] flex flex-col items-center justify-center p-10 text-center">
                    <div class="bg-blue-50 p-4 rounded-full mb-4">
                        <i data-lucide="layers" size="40" class="text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Section</h3>
                    <p class="text-gray-500 max-w-md mb-6">
                        Tambahkan section (Misal: Listening, Reading) untuk mulai mengisi soal ujian.
                    </p>
                    <button onclick="document.getElementById('modalSection').classList.remove('hidden')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                        <i data-lucide="plus"></i> Tambah Section Baru
                    </button>
                </div>

            @endif

        </main>
    </div>

    {{-- MODAL POPUP TAMBAH SECTION --}}
    <div id="modalSection" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800">Tambah Section Baru</h3>
                <button onclick="document.getElementById('modalSection').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            
            <form action="{{ route('guru.ujian.section.store', $ujian->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Section</label>
                    <input type="text" name="judul" placeholder="Contoh: Section 1 - Listening Comprehension" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" value="20" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    <p class="text-xs text-gray-500 mt-1">Siswa akan otomatis pindah section jika waktu habis.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalSection').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-md">
                        Simpan Section
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>