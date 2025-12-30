<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
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
    <div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 transition-all duration-300 shadow-xl">
        <div class="text-white mb-4 cursor-pointer hover:scale-110 transition duration-300">
            <i data-lucide="menu" size="28"></i>
        </div>

        <a href="?page=home" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'home' ? 'bg-white/20 shadow-lg ring-1 ring-white/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}"
           title="Dashboard">
            <i data-lucide="home" size="28" class="text-white"></i>
        </a>

        <a href="?page=test" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'test' ? 'bg-white/20 shadow-lg ring-1 ring-white/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}"
           title="Ujian">
            <i data-lucide="edit-3" size="28" class="text-white"></i>
        </a>

        <a href="?page=analysis" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'analysis' ? 'bg-white/20 shadow-lg ring-1 ring-white/30' : 'text-white/70 hover:text-white hover:bg-white/10' }}"
           title="Analisis">
            <i data-lucide="bar-chart-2" size="28" class="text-white"></i>
        </a>

        <div class="mt-auto flex flex-col gap-6 mb-4">
            <div class="relative group">
                <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer border-2 border-transparent group-hover:border-white transition shadow-md">
                    <span class="text-purple-700 font-bold uppercase">
                        {{ substr(Auth::user()->username, 0, 1) }}
                    </span>
                </div>

                {{-- Logout Tooltip --}}
                <div class="hidden group-hover:block absolute left-11 bottom-0 bg-white shadow-xl rounded-xl p-2 border border-gray-200 w-32 z-50 animate-fade-in">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm font-large text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition flex items-center gap-2">
                            <i data-lucide="log-out" size="14"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-20 p-8 transition-all duration-300 min-h-screen">

        {{-- HEADER DASHBOARD --}}
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Siswa</h1>
                <p class="text-gray-500 text-sm mt-1">Selamat datang kembali, Semangat belajar!</p>
            </div>
            <div class="bg-white px-5 py-2.5 rounded-xl shadow-sm border border-gray-100 text-gray-700 font-medium flex items-center gap-2">
                <i data-lucide="calendar" size="18" class="text-[#004e92]"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </div>
        </header>

        {{-- HALAMAN HOME --}}
        @if($page == 'home')
            <section class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 animate-fade-in">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1 duration-300">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Total Attempts</h3>
                    <p class="text-3xl font-bold text-[#004e92]">{{ $totalAttempts ?? 0 }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1 duration-300">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Reading Avg</h3>
                    <p class="text-3xl font-bold text-emerald-600">{{ number_format($readingAvg ?? 0, 1) }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1 duration-300">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Listening Avg</h3>
                    <p class="text-3xl font-bold text-amber-500">{{ number_format($listeningAvg ?? 0, 1) }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1 duration-300">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Writing Avg</h3>
                    <p class="text-3xl font-bold text-rose-500">{{ number_format($writingAvg ?? 0, 1) }}</p>
                </div>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i data-lucide="pie-chart" size="20" class="text-gray-400"></i> Score Distribution
                    </h2>
                    <div class="flex justify-center items-center h-48 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        Chart Belum Tersedia
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i data-lucide="trending-up" size="20" class="text-gray-400"></i> Score Trends
                    </h2>
                    <div class="flex justify-center items-center h-48 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        No Data Available
                    </div>
                </div>
            </section>

            <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 animate-fade-in" style="animation-delay: 0.2s;">
                <h2 class="font-bold text-gray-700 mb-4">Recent Activity</h2>
                
                @forelse($recentNilais ?? [] as $nilai)
                    <div class="flex items-center justify-between p-4 mb-2 bg-gray-50 hover:bg-gray-100 rounded-xl transition duration-200">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $nilai->ujian->judul ?? 'Ujian' }}</h4>
                            <p class="text-xs text-gray-500">{{ $nilai->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="font-bold text-[#004e92] bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">{{ $nilai->skor_total }} Poin</span>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8 italic">Belum ada aktivitas ujian.</div>
                @endforelse
            </section>

        {{-- HALAMAN UJIAN (REVISI FINAL) --}}
        @elseif($page == 'test')
            <div class="animate-fade-in">
                
                {{-- SATU KOTAK BESAR UNTUK SEMUA --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[500px]">
                    
                    @if($ujianAktif->count() > 0)
                        {{-- KONDISI 1: ADA UJIAN --}}
                        <div class="mb-6 border-b border-gray-100 pb-4">
                            <h2 class="text-xl font-bold text-gray-800">Ujian Anda</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($ujianAktif as $ujian)
                                @php
                                    // Cek Status Waktu
                                    $isStarted = now() >= $ujian->waktu_mulai;
                                @endphp

                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 hover:shadow-md transition group h-full flex flex-col hover:-translate-y-1 duration-300">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="p-3 {{ $isStarted ? 'bg-blue-100 text-[#004e92]' : 'bg-gray-200 text-gray-500' }} rounded-xl transition duration-300">
                                            <i data-lucide="book-open" size="24"></i>
                                        </div>
                                        
                                        @if($isStarted)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full animate-pulse">
                                                AKTIF
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">
                                                AKAN DATANG
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 leading-snug" title="{{ $ujian->judul }}">
                                        {{ $ujian->judul }}
                                    </h3>
                                    
                                    <div class="space-y-3 text-sm text-gray-500 mb-6 flex-1">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" size="16" class="text-gray-400"></i>
                                            <span>Durasi: <span class="font-medium text-gray-700">{{ $ujian->durasi_menit }} Menit</span></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" size="16" class="text-gray-400"></i>
                                            @if(!$isStarted)
                                                <span class="text-yellow-600 font-medium">Mulai: {{ $ujian->waktu_mulai->format('d M H:i') }}</span>
                                            @else
                                                <span>Selesai: {{ $ujian->waktu_selesai->format('d M H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isStarted)
                                        <a href="{{ route('siswa.ujian.detail', $ujian->id) }}" 
                                           class="block w-full text-center py-3 bg-[#004e92] hover:bg-[#003d73] text-white font-semibold rounded-xl transition shadow-sm hover:shadow active:scale-95 duration-200">
                                            Kerjakan Sekarang
                                        </a>
                                    @else
                                        <button disabled class="block w-full text-center py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                            Belum Dimulai
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    @else
                        {{-- KONDISI 2: TIDAK ADA UJIAN (KOSONG) --}}
                        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                            
                            <div class="inline-block p-4 bg-gray-50 rounded-full mb-4 text-gray-400">
                                <i data-lucide="inbox" size="48"></i>
                            </div>

                            <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Ujian Aktif</h3>
                            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                                Saat ini belum ada jadwal ujian aktif yang tersedia untuk Anda kerjakan. Silakan cek arsip ujian lengkap jika perlu.
                            </p>

                            <a href="{{ route('siswa.ujian.index') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-[#004e92] hover:bg-[#003d73] text-white font-medium rounded-xl transition shadow-md hover:shadow-lg active:scale-95 duration-200">
                                <i data-lucide="list" size="20"></i>
                                Lihat Semua Daftar Ujian
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        {{-- HALAMAN ANALISIS --}}
        @elseif($page == 'analysis')
            <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center animate-fade-in">
                <div class="inline-block p-4 bg-purple-50 rounded-full mb-4">
                    <i data-lucide="bar-chart-2" size="40" class="text-purple-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Analisis Lengkap</h2>
                <p class="text-gray-500">Grafik perkembangan nilai detail akan muncul di sini.</p>
            </div>
        @endif

        {{-- FOOTER --}}
        <footer class="text-center mt-12 pb-6 text-gray-400 text-xs border-t border-gray-200 pt-6">
            Login sebagai: <strong class="text-gray-600">{{ Auth::user()->username }}</strong> • 
            Role: <span class="capitalize">{{ Auth::user()->role->value ?? 'Siswa' }}</span>
        </footer>
        
        {{-- HASIL TERAKHIR NOTIFICATION --}}
        @if ($sesiTerakhir && $sesiTerakhir->status === 'selesai')
            <div class="mt-8 bg-green-50 border border-green-200 p-4 rounded-xl flex justify-between items-center shadow-sm animate-fade-in" style="animation-delay: 0.5s;">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg text-green-700">
                        <i data-lucide="check-circle" size="20"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-green-800 text-sm">Hasil Ujian Terakhir</h3>
                        <p class="text-xs text-green-700">{{ $sesiTerakhir->ujian->judul }}</p>
                    </div>
                </div>
                <a href="{{ route('siswa.ujian.hasil', $sesiTerakhir->id) }}" class="px-4 py-2 bg-white text-green-700 font-semibold text-sm rounded-lg border border-green-200 hover:bg-green-50 transition shadow-sm">
                    Lihat Hasil
                </a>
            </div>
        @endif

    </main>

    <script>
        lucide.createIcons();

        // Prevent Back Button Logic
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
    
</body>
</html>