@props(['active' => 'dashboard', 'role' => 'siswa'])

<div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50">

    {{-- Menu Icon --}}
    <div class="text-white mb-4 cursor-pointer">
        <i data-lucide="menu" size="28"></i>
    </div>

    {{-- Navigation Items (Dynamic based on Role) --}}
    @if($role === 'admin')
        {{-- Admin Navigation --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'dashboard' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="layout-dashboard" size="28" class="text-white"></i>
        </a>

        <a href="{{ route('admin.ujian.index') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'ujian' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="clipboard-list" size="28" class="text-white"></i>
        </a>

        <a href="{{ route('admin.stats') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'stats' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="bar-chart-2" size="28" class="text-white"></i>
        </a>

    @elseif($role === 'guru')
        {{-- Guru Navigation --}}
        <a href="{{ route('guru.dashboard') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'dashboard' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="home" size="28" class="text-white"></i>
        </a>

        <a href="{{ route('guru.soal.index') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'soal' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="file-text" size="28" class="text-white"></i>
        </a>

      <a href="#" 
            onclick="alert('Fitur Statistik segera hadir!')"
            class="p-3 rounded-xl cursor-pointer transition {{ $active === 'stats' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
                <i data-lucide="bar-chart-2" size="28" class="text-white"></i>
            </a>
    @else
        {{-- Siswa Navigation --}}
        <a href="{{ route('siswa.dashboard') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'home' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="home" size="28" class="text-white"></i>
        </a>

        <a href="{{ route('siswa.ujian.index') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'test' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="edit-3" size="28" class="text-white"></i>
        </a>

        <a href="{{ route('siswa.nilai') }}" 
           class="p-3 rounded-xl cursor-pointer transition {{ $active === 'analysis' ? 'bg-white/20' : 'text-white/70 hover:text-white' }}">
            <i data-lucide="bar-chart-2" size="28" class="text-white"></i>
        </a>
    @endif

    {{-- Bottom Section (Settings + User) --}}
    <div class="mt-auto flex flex-col items-center gap-6 mb-4">

        {{-- Settings Icon --}}
        <i data-lucide="settings" size="28" class="text-white/70 hover:text-white cursor-pointer transition"></i>

        {{-- User Dropdown dengan Alpine.js --}}
        <div class="relative" x-data="{ open: false }">
            <div @click="open = !open" 
                 class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer hover:bg-purple-300 transition">
                <i data-lucide="user" size="22" class="text-purple-700"></i>
            </div>

            {{-- Dropdown Menu --}}
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="absolute left-[88px] bottom-0 bg-white shadow-lg rounded-xl p-3 border border-gray-200 w-40"
                 style="display: none;">
                
                <div class="text-xs text-gray-500 px-3 pb-2 border-b mb-2">
            {{-- Pastikan atribut nama sesuai database, biasanya name atau username --}}
            <div class="font-semibold text-gray-700">{{ auth()->user()->username ?? auth()->user()->name }}</div>
            
            {{-- FIX: Tambahkan ->value karena role adalah Enum --}}
            <div class="text-[10px]">{{ ucfirst(auth()->user()->role->value) }}</div>
            </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full text-sm font-medium text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition text-center">
                        <i data-lucide="log-out" size="16" class="inline mr-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>