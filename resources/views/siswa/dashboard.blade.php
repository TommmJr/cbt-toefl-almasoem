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
    </style>
</head>

<body class="bg-gray-100 font-sans flex text-slate-800">

    <div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 transition-all duration-300">
        <div class="text-white mb-4 cursor-pointer hover:scale-110 transition">
            <i data-lucide="menu" size="28"></i>
        </div>

        <a href="?page=home" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'home' ? 'bg-white/20 shadow-lg' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
            <i data-lucide="home" size="28" class="text-white"></i>
        </a>

        <a href="?page=test" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'test' ? 'bg-white/20 shadow-lg' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
            <i data-lucide="edit-3" size="28" class="text-white"></i>
        </a>

        <a href="?page=analysis" 
           class="p-3 rounded-xl cursor-pointer transition {{ $page == 'analysis' ? 'bg-white/20 shadow-lg' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
            <i data-lucide="bar-chart-2" size="28" class="text-white"></i>
        </a>

        <div class="mt-auto flex flex-col gap-6 mb-4">
            
            <div class="relative group">
                <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer border-2 border-transparent group-hover:border-white transition">
                    <span class="text-purple-700 font-bold uppercase">
                        {{ substr(Auth::user()->username, 0, 1) }}
                    </span>
                </div>

                <div class="hidden group-hover:block absolute left-12 bottom-0 bg-white shadow-xl rounded-xl p-2 border border-gray-200 w-32 z-70>">
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

    <main class="flex-1 ml-20 p-8 transition-all duration-300">

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

        @if($page == 'home')
            <section class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition card-hover">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Total Attempts</h3>
                    <p class="text-3xl font-bold text-[#004e92]">{{ $totalAttempts ?? 0 }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition card-hover">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Reading Avg</h3>
                    <p class="text-3xl font-bold text-emerald-600">{{ number_format($readingAvg ?? 0, 1) }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition card-hover">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Listening Avg</h3>
                    <p class="text-3xl font-bold text-amber-500">{{ number_format($listeningAvg ?? 0, 1) }}</p>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition card-hover">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Speaking Avg</h3>
                    <p class="text-3xl font-bold text-rose-500">{{ number_format($writingAvg ?? 0, 1) }}</p>
                </div>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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

            <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <h2 class="font-bold text-gray-700 mb-4">Recent Activity</h2>
                
                @forelse($recentNilais ?? [] as $nilai)
                    <div class="flex items-center justify-between p-4 mb-2 bg-gray-50 rounded-xl">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $nilai->ujian->judul ?? 'Ujian' }}</h4>
                            <p class="text-xs text-gray-500">{{ $nilai->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="font-bold text-[#004e92]">{{ $nilai->skor_total }} Poin</span>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">Belum ada aktivitas ujian.</div>
                @endforelse
            </section>

        @elseif($page == 'test')
            <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center animate-fade-in">
                <div class="inline-block p-4 bg-blue-50 rounded-full mb-4">
                    <i data-lucide="file-question" size="40" class="text-[#004e92]"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Halaman Ujian</h2>
                <p class="text-gray-500">Silakan pilih ujian yang tersedia di bawah ini.</p>
                </div>

        @elseif($page == 'analysis')
            <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center animate-fade-in">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Analisis Lengkap</h2>
                <p class="text-gray-500">Grafik perkembangan nilai detail akan muncul di sini.</p>
            </div>
        @endif

        <footer class="text-center mt-12 pb-6 text-gray-400 text-xs">
            Login sebagai: <strong class="text-gray-600">{{ Auth::user()->username }}</strong> • 
            Role: <span class="capitalize">{{ Auth::user()->role->value ?? 'Siswa' }}</span>
        </footer>

    </main>

    <script>
        lucide.createIcons();

        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
    
</body>
</html>