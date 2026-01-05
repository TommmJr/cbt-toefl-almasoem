<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ujian - {{ $ujian->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body class="bg-gray-100 font-sans flex text-slate-800">

    {{-- SIDEBAR --}}
    <div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 shadow-xl">
        <div class="text-white mb-4 cursor-pointer hover:scale-110 transition duration-300">
            <i data-lucide="menu" size="28"></i>
        </div>

        <a href="{{ route('siswa.dashboard') }}?page=home" 
           class="p-3 rounded-xl cursor-pointer transition text-white/70 hover:text-white hover:bg-white/10"
           title="Dashboard">
            <i data-lucide="home" size="28"></i>
        </a>

        <a href="{{ route('siswa.ujian.index') }}" 
           class="p-3 rounded-xl cursor-pointer transition bg-white/20 shadow-lg ring-1 ring-white/30 text-white"
           title="Ujian">
            <i data-lucide="edit-3" size="28"></i>
        </a>

        {{-- BAGIAN USER PROFILE & LOGOUT --}}
        <div class="mt-auto flex flex-col gap-6 mb-4">
            <div class="relative group">
                {{-- Avatar --}}
                <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer border-2 border-transparent group-hover:border-white transition shadow-md">
                    <span class="text-purple-700 font-bold uppercase">
                        {{ substr(Auth::user()->username ?? 'S', 0, 1) }}
                    </span>
                </div>

                {{-- Menu Logout (Muncul pas di-hover) --}}
                <div class="hidden group-hover:block absolute left-10 bottom-0 bg-white shadow-xl rounded-xl p-2 border border-gray-200 w-32 z-50 animate-fade-in">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm font-medium text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition flex items-center gap-2">
                            <i data-lucide="log-out" size="14"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-20 p-8 min-h-screen transition-all">

        {{-- BREADCRUMB & BACK BUTTON --}}
        <div class="mb-8 flex items-center gap-3">
            <a href="{{ route('siswa.ujian.index') }}" class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Ujian</h1>
                <p class="text-gray-500 text-sm">Informasi lengkap sebelum memulai tes.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
            
            {{-- KOLOM KIRI: INFO UTAMA --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- CARD HEADER UJIAN --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-5">
                        <i data-lucide="file-check" size="120"></i>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-[#004e92] text-xs font-bold rounded-full">
                                EXAM
                            </span>
                            <span class="text-gray-400 text-sm flex items-center gap-1">
                                <i data-lucide="clock" size="14"></i> {{ $ujian->durasi_menit }} Menit Total
                            </span>
                        </div>

                        <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ $ujian->judul }}</h2>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $ujian->deskripsi ?? 'Tidak ada deskripsi ujian.' }}
                        </p>
                    </div>
                </div>

                {{-- CARD LIST SECTION --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i data-lucide="layers" size="20" class="text-[#004e92]"></i>
                        Daftar Section Ujian
                    </h3>

                    <div class="space-y-4">
                        @foreach ($ujian->sections as $section)
                            @php
                                $icon = match($section->tipe_section->value ?? 'default') {
                                    'listening' => 'headphones',
                                    'reading'   => 'book-open',
                                    'structure' => 'pen-tool',
                                    'writing'   => 'edit',
                                    default     => 'file-text'
                                };
                            @endphp
                            
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-200 transition group">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm text-[#004e92] group-hover:scale-110 transition">
                                    <i data-lucide="{{ $icon }}" size="24"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="font-bold text-gray-800 text-sm group-hover:text-[#004e92] transition">
                                        {{ $section->judul_section }}
                                    </h4>
                                    <p class="text-xs text-gray-500 capitalize">{{ $section->tipe_section->value ?? 'Standard' }} Section</p>
                                </div>
                                <div class="text-right">
                                    <span class="block font-bold text-gray-700">{{ $section->durasi_menit }}</span>
                                    <span class="text-xs text-gray-400">Menit</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: CTA --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 sticky top-8">
                    <h3 class="font-bold text-gray-800 mb-4">Persiapan</h3>
                    
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <i data-lucide="check-circle" size="18" class="text-green-500 shrink-0 mt-0.5"></i>
                            Pastikan koneksi internet stabil.
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <i data-lucide="check-circle" size="18" class="text-green-500 shrink-0 mt-0.5"></i>
                            Gunakan headset untuk sesi Listening.
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <i data-lucide="check-circle" size="18" class="text-green-500 shrink-0 mt-0.5"></i>
                            Dilarang pindah tab (Auto-lock aktif).
                        </li>
                    </ul>

                    <div class="pt-6 border-t border-gray-100">
                        <a href="{{ route('siswa.ujian.akses') }}" 
                           class="w-full flex justify-center items-center gap-2 py-4 bg-[#004e92] hover:bg-[#003d73] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95">
                            Mulai Ujian <i data-lucide="arrow-right" size="20"></i>
                        </a>
                        <p class="text-xs text-center text-gray-400 mt-3">
                            Anda akan diminta memasukkan Token.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <footer class="text-center mt-12 pb-6 text-gray-400 text-xs">
            &copy; {{ date('Y') }} CBT Al-Ma'soem. All rights reserved.
        </footer>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>