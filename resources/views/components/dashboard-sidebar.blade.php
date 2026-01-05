@props(['role' => 'siswa', 'active' => 'dashboard'])

{{-- Sidebar Container --}}
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

    {{-- 2. Navigation Menu --}}
    <nav class="flex-1 py-6 flex flex-col gap-2 px-3 overflow-y-auto">
        
        {{-- =========================================== --}}
        {{-- MENU KHUSUS GURU --}}
        {{-- =========================================== --}}
        @if($role === 'guru')
            {{-- Dashboard --}}
            <a href="{{ route('guru.dashboard') }}" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $active == 'dashboard' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Dashboard</span>
                @if($active == 'dashboard') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>

            {{-- Manajemen Ujian --}}
            <a href="{{ route('guru.ujian.index') }}" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $active == 'ujian' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="file-text" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Manajemen Ujian</span>
                @if($active == 'ujian') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>
            
            {{-- Statistik --}}
            <a href="#" onclick="alert('Fitur Statistik Segera Hadir!')"
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap text-white/70 hover:bg-white/10 hover:text-white">
                <i data-lucide="bar-chart-2" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Analisis Nilai</span>
            </a>
        @endif


        {{-- =========================================== --}}
        {{-- MENU KHUSUS SISWA (INI YANG DIBENERIN) --}}
        {{-- =========================================== --}}
        @if($role === 'siswa')
            {{-- Beranda --}}
            <a href="{{ route('siswa.dashboard') }}" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $active == 'dashboard' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="home" class="w-6 h-6 shrink-0"></i>
                {{-- Penambahan class: opacity-0 group-hover:opacity-100 --}}
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Beranda</span>
                @if($active == 'dashboard') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>

            {{-- Ujian Saya --}}
            <a href="{{ route('siswa.ujian.index') }}" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap
               {{ $active == 'ujian' ? 'bg-white/20 text-white shadow-inner' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="pen-tool" class="w-6 h-6 shrink-0"></i>
                {{-- Penambahan class: opacity-0 group-hover:opacity-100 --}}
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Ujian Saya</span>
                @if($active == 'ujian') <div class="absolute left-0 top-3 bottom-3 w-1 bg-yellow-400 rounded-r-full"></div> @endif
            </a>
            
            {{-- Riwayat --}}
            <a href="#" 
               class="flex items-center gap-4 px-3 py-3.5 rounded-xl transition-all duration-200 relative overflow-hidden whitespace-nowrap text-white/70 hover:bg-white/10 hover:text-white">
                <i data-lucide="history" class="w-6 h-6 shrink-0"></i>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-medium">Riwayat</span>
            </a>
        @endif

    </nav>

    {{-- 3. User & Logout Area --}}
    <div class="p-4 border-t border-white/10 bg-[#00427a]">
        <div class="flex items-center gap-3 overflow-hidden">
            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0">
                <i data-lucide="user" size="20"></i>
            </div>
            
            {{-- Info User (Muncul pas di-hover) --}}
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex-1 min-w-0">
                <p class="text-sm font-bold truncate">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-white/60 truncate uppercase">{{ Auth::user()->role->value ?? 'Siswa' }}</p>
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