<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - CBT TOEFL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        nav::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="bg-gray-100 font-sans flex text-slate-800">

    {{-- SIDEBAR SISWA (UPDATED: GAYA EXPANDABLE ALA GURU) --}}
    <aside class="w-20 hover:w-64 bg-[#004e92] text-white h-screen fixed left-0 top-0 transition-all duration-300 z-50 flex flex-col group shadow-2xl overflow-hidden font-poppins">
        
        {{-- 1. Logo Area --}}
        <div class="h-20 flex items-center justify-center border-b border-white/10 relative shrink-0">
            {{-- Ikon Logo --}}
            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center absolute left-5 transition-all duration-300">
                <i data-lucide="graduation-cap" class="text-white w-6 h-6"></i>
            </div>
            
            {{-- Teks Logo (Muncul pas di-hover) --}}
            <span class="opacity-0 group-hover:opacity-100 transition-all duration-500 absolute left-20 font-bold text-xl tracking-wide whitespace-nowrap">
                CBT AL-MA'SOEM
            </span>
        </div>

        {{-- 2. Menu Navigasi --}}
        <nav class="flex-1 py-6 flex flex-col gap-2 px-3 overflow-y-auto">

            {{-- Menu: Beranda --}}
            <a href="?page=home" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $page == 'home' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="home" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Beranda</span>
                @if($page == 'home') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>

            {{-- Menu: Ujian Saya --}}
            <a href="?page=test" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $page == 'test' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="pen-tool" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Ujian Saya</span>
                @if($page == 'test') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>

            {{-- Menu: Analisis --}}
            <a href="?page=analysis" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $page == 'analysis' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="bar-chart-2" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Analisis Nilai</span>
                @if($page == 'analysis') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>

        </nav>

        {{-- 3. User & Logout Area --}}
        <div class="p-4 border-t border-white/10 bg-[#00427a]">
            <div class="flex items-center gap-3 overflow-hidden">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold shrink-0">
                    {{ substr(Auth::user()->username ?? Auth::user()->name, 0, 1) }}
                </div>
                
                {{-- Info User (Muncul pas di-hover) --}}
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex-1 min-w-0">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->username ?? Auth::user()->name }}</p>
                    <p class="text-xs text-white/60 truncate uppercase">Siswa</p>
                </div>
            </div>

            {{-- Tombol Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 px-3 py-2 rounded-lg text-red-200 hover:bg-red-500/20 hover:text-white transition-all duration-200 group/button whitespace-nowrap">
                    <i data-lucide="log-out" class="w-6 h-6 shrink-0"></i>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium text-sm">Keluar Aplikasi</span>
                </button>
            </form>
        </div>

    </aside>

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

        {{-- HALAMAN UJIAN --}}
        @elseif($page == 'test')
            <div class="animate-fade-in">
                
                {{-- SATU KOTAK BESAR UNTUK SEMUA --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 min-h-[500px]">
                    
                    @if(isset($ujianAktif) && $ujianAktif->count() > 0)
                        {{-- KONDISI 1: ADA UJIAN --}}
                        <div class="mb-6 border-b border-gray-100 pb-4">
                            <h2 class="text-xl font-bold text-gray-800">Ujian Anda</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($ujianAktif as $ujian)
                @php
                    $sesiSiswa = \App\Models\SesiUjian::where('ujian_id', $ujian->id)
                        ->where('siswa_id', $user->siswa->id ?? null)
                        ->first();

                    $isStarted = now() >= $ujian->waktu_mulai;
                    // Status Selesai: Bisa ngecek string atau value Enum
                    $isFinished = $sesiSiswa && ($sesiSiswa->status === \App\Enums\StatusUjian::SELESAI || $sesiSiswa->status == 'selesai');
                @endphp

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 hover:shadow-md transition group h-full flex flex-col hover:-translate-y-1 duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 {{ $isStarted ? 'bg-blue-100 text-[#004e92]' : 'bg-gray-200 text-gray-500' }} rounded-xl transition duration-300">
                            <i data-lucide="book-open" size="24"></i>
                        </div>
                        
                        @if($isFinished)
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                SUDAH DIKERJAKAN
                            </span>
                        @elseif($isStarted)
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
                            @if($isFinished)
                                <span class="text-blue-600 font-medium italic">Ujian selesai pada {{ $sesiSiswa->updated_at->format('d M H:i') }}</span>
                            @elseif(!$isStarted)
                                <span class="text-yellow-600 font-medium">Mulai: {{ $ujian->waktu_mulai->format('d M H:i') }}</span>
                            @else
                                <span>Selesai: {{ $ujian->waktu_selesai->format('d M H:i') }}</span>
                            @endif
                        </div>
                    </div>

                    @if($isFinished)
                        {{-- Tombol Lihat Hasil kalau sudah beres --}}
                        <a href="{{ route('siswa.ujian.hasil', $sesiSiswa->id) }}" 
                        class="block w-full text-center py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition shadow-sm hover:shadow active:scale-95 duration-200">
                            Lihat Hasil
                        </a>
                    @elseif($isStarted)
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

                            <a href="?page=test" 
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
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 animate-fade-in">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i data-lucide="line-chart" class="text-purple-600 w-6 h-6"></i>
                        </div>
                        Statistik Performa TOEFL
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Grafik progres skor total Anda (Standard TOEFL ITP 310-677).</p>
                </div>
            </div>
            
            <div class="h-[450px] w-full bg-slate-50/50 p-6 rounded-2xl border border-dashed border-gray-200 relative">
                {{-- Cek dulu datanya ada gak, kalo gak ada tampilin pesan kosong --}}
                @if(count($chartData['scores']) > 0)
                    <canvas id="scoreChart"></canvas>
                @else
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i data-lucide="bar-chart" size="48" class="mb-4 opacity-20"></i>
                        <p class="italic">Belum ada data nilai untuk dianalisis.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Script Chart.js (Pake CDN yang pasti-pasti aja) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        
        <script>
            // Pake window.onload biar pasti semua library (Chart.js & Lucide) beres di-load
            window.onload = function() {
                const canvas = document.getElementById('scoreChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                
                // Siapin Data dari Laravel
                const labels = {!! json_encode($chartData['labels'] ?? []) !!};
                const scores = {!! json_encode($chartData['scores'] ?? []) !!};

                // Gradient Fill
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(0, 78, 146, 0.3)');
                gradient.addColorStop(1, 'rgba(0, 78, 146, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Skor Total',
                            data: scores,
                            borderColor: '#004e92',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#004e92',
                            pointBorderWidth: 2,
                            pointHoverRadius: 9
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#1e293b',
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return ' Skor: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: 300,
                                max: 680,
                                ticks: { stepSize: 50 },
                                grid: { borderDash: [5, 5], color: '#e2e8f0' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
                
                // Re-init icons kalo ada yang belum ke-render
                if(typeof lucide !== 'undefined') lucide.createIcons();
            };
        </script>
    @endif

        {{-- FOOTER --}}
        <footer class="text-center mt-12 pb-6 text-gray-400 text-xs border-t border-gray-200 pt-6">
            Login sebagai: <strong class="text-gray-600">{{ Auth::user()->username }}</strong> • 
            Role: <span class="capitalize">{{ Auth::user()->role->value ?? 'Siswa' }}</span>
        </footer>
        
        {{-- HASIL TERAKHIR NOTIFICATION --}}
        @if (isset($sesiTerakhir) && $sesiTerakhir && $sesiTerakhir->status === 'selesai')
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
    </script>
    
</body>
</html>