<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Ujian - CBT Toefl</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Animasi halus saat load */
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body class="bg-gray-100 font-sans flex text-slate-800">

    {{-- SIDEBAR (Link sudah disesuaikan agar kembali ke Dashboard dengan benar) --}}
    <div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 shadow-xl">
        {{-- Logo / Menu Icon --}}
        <div class="text-white mb-4 cursor-pointer hover:scale-110 transition duration-300">
            <i data-lucide="menu" size="28"></i>
        </div>

        {{-- Home Link --}}
        <a href="{{ route('siswa.dashboard') }}?page=home" 
           class="p-3 rounded-xl cursor-pointer transition text-white/70 hover:text-white hover:bg-white/10"
           title="Dashboard">
            <i data-lucide="home" size="28"></i>
        </a>

        {{-- Test Link (Sedang Aktif karena ini halaman Ujian) --}}
        <a href="{{ route('siswa.dashboard') }}?page=test" 
           class="p-3 rounded-xl cursor-pointer transition bg-white/20 shadow-lg ring-1 ring-white/30 text-white"
           title="Ujian">
            <i data-lucide="edit-3" size="28"></i>
        </a>

        {{-- Analysis Link --}}
        <a href="{{ route('siswa.dashboard') }}?page=analysis" 
           class="p-3 rounded-xl cursor-pointer transition text-white/70 hover:text-white hover:bg-white/10"
           title="Analisis">
            <i data-lucide="bar-chart-2" size="28"></i>
        </a>

        {{-- Profile & Logout --}}
        <div class="mt-auto flex flex-col gap-6 mb-4">
            <div class="relative group">
                <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer border-2 border-transparent group-hover:border-white transition shadow-md">
                    <span class="text-purple-700 font-bold uppercase">
                        {{ substr(Auth::user()->username, 0, 1) }}
                    </span>
                </div>

                {{-- Logout Tooltip --}}
                <div class="hidden group-hover:block absolute left-14 bottom-0 bg-white shadow-xl rounded-xl p-2 border border-gray-200 w-32 z-50 animate-fade-in">
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

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Arsip Semua Ujian</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <a href="{{ route('siswa.dashboard') }}?page=test" class="hover:text-[#004e92] transition">Dashboard</a>
                    <span>/</span>
                    <span>Daftar Ujian</span>
                </div>
            </div>

            {{-- Search Bar (Opsional, form GET) --}}
            <form action="" method="GET" class="relative w-full md:w-auto">
                <input type="text" name="q" placeholder="Cari nama ujian..." value="{{ request('q') }}"
                       class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#004e92]/20 focus:border-[#004e92] w-full md:w-64 transition shadow-sm">
                <i data-lucide="search" size="18" class="absolute left-3 top-3 text-gray-400"></i>
            </form>
        </div>

        {{-- CONTENT BOX --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[500px] animate-fade-in">
            
            @if(isset($ujians) && $ujians->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($ujians as $ujian)
                        @php
                            // Cek Status Waktu
                            $now = now();
                            $isUpcoming = $now < $ujian->waktu_mulai;
                            $isStarted  = $now >= $ujian->waktu_mulai && $now <= $ujian->waktu_selesai;
                            $isEnded    = $now > $ujian->waktu_selesai;
                        @endphp

                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 hover:shadow-md transition group h-full flex flex-col hover:-translate-y-1 duration-300 relative overflow-hidden">
                            
                            {{-- Status Badge --}}
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-white shadow-sm rounded-xl text-gray-600 group-hover:text-[#004e92] transition">
                                    <i data-lucide="file-text" size="24"></i>
                                </div>
                                
                                @if($isUpcoming)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">
                                        AKAN DATANG
                                    </span>
                                @elseif($isStarted)
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full animate-pulse">
                                        SEDANG AKTIF
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-200 text-gray-600 text-xs font-bold rounded-full">
                                        SELESAI
                                    </span>
                                @endif
                            </div>

                            {{-- Judul --}}
                            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 leading-snug" title="{{ $ujian->judul }}">
                                {{ $ujian->judul }}
                            </h3>
                            
                            {{-- Info Meta --}}
                            <div class="space-y-3 text-sm text-gray-500 mb-6 flex-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="clock" size="16" class="text-gray-400"></i>
                                    <span>Durasi: <span class="font-medium text-gray-700">{{ $ujian->durasi_menit }} Menit</span></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" size="16" class="text-gray-400"></i>
                                    @if($isUpcoming)
                                        <span class="text-yellow-600">Mulai: {{ $ujian->waktu_mulai->format('d M Y, H:i') }}</span>
                                    @elseif($isStarted)
                                        <span class="text-green-600">Selesai: {{ $ujian->waktu_selesai->format('d M Y, H:i') }}</span>
                                    @else
                                        <span>Berakhir: {{ $ujian->waktu_selesai->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Button --}}
                            @if($isStarted)
                                <a href="{{ route('siswa.ujian.detail', $ujian->id) }}" 
                                   class="block w-full text-center py-3 bg-[#004e92] hover:bg-[#003d73] text-white font-semibold rounded-xl transition shadow-sm hover:shadow active:scale-95 duration-200">
                                    Kerjakan Sekarang
                                </a>
                            @elseif($isUpcoming)
                                <button disabled class="block w-full text-center py-3 bg-yellow-50 text-yellow-600 font-semibold rounded-xl cursor-not-allowed border border-yellow-100">
                                    Belum Dimulai
                                </button>
                            @else
                                <button disabled class="block w-full text-center py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                    Waktu Habis
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Pagination (Jika pakai paginate di controller) --}}
                @if(method_exists($ujians, 'links'))
                    <div class="mt-8">
                        {{ $ujians->withQueryString()->links() }}
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                    <div class="inline-block p-6 bg-gray-50 rounded-full mb-6 text-gray-300">
                        <i data-lucide="folder-open" size="64"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Arsip Kosong</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8">
                        Belum ada data ujian yang tersedia di arsip saat ini.
                    </p>
                    <a href="{{ route('siswa.dashboard') }}?page=home" 
                       class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif
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
```