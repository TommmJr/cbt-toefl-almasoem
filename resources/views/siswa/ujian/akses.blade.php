<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukan Token Ujian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        /* Style khusus buat input token biar kayak OTP */
        .token-input { letter-spacing: 0.5em; text-align: center; font-family: monospace; }
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

        {{-- Menu Ujian Aktif --}}
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
    </div> {{-- <--- PENUTUP SIDEBAR (PENTING!) --}}

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-20 p-8 min-h-screen flex items-center justify-center relative overflow-hidden">
        
        {{-- Background Decoration --}}
        <div class="absolute top-0 left-0 w-full h-64 bg-[#004e92]/5 -z-10 skew-y-3 transform origin-top-left"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10"></div>

        <div class="w-full max-w-lg animate-fade-in">
            
            {{-- Tombol Kembali --}}
            <div class="mb-6">
                <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#004e92] transition font-medium text-sm">
                    <i data-lucide="arrow-left" size="18"></i> Kembali ke Daftar Ujian
                </a>
            </div>

            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-gray-100 relative">
                
                {{-- Header Card --}}
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-[#004e92]">
                        <i data-lucide="lock-keyhole" size="40"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Akses Ujian</h1>
                    <p class="text-gray-500 text-sm mt-2">Silakan masukkan 6 digit kode token yang diberikan oleh pengawas.</p>
                </div>

                {{-- Alert Error --}}
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg flex items-start gap-3 animate-pulse">
                        <i data-lucide="alert-circle" class="text-red-500 shrink-0 mt-0.5" size="18"></i>
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- Form Token --}}
                <form method="POST" action="{{ route('siswa.ujian.akses.post') }}" autocomplete="off">
                    @csrf

                    <div class="mb-8">
                        <label class="block text-gray-700 font-bold text-xs uppercase tracking-wider mb-3 text-center">
                            Kode Token
                        </label>
                        
                        <div class="relative">
                            <input type="text" 
                                   name="kode_token" 
                                   maxlength="6" 
                                   required 
                                   autofocus
                                   placeholder="_ _ _ _ _ _"
                                   class="token-input w-full py-4 px-6 text-3xl font-bold text-gray-800 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-[#004e92] focus:ring-4 focus:ring-blue-500/10 transition placeholder-gray-300 uppercase"
                                   oninput="this.value = this.value.toUpperCase()">
                            
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <i data-lucide="key" size="20"></i>
                            </div>
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-3">
                            Pastikan token sesuai dengan sesi ujian saat ini.
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full py-4 bg-[#004e92] hover:bg-[#003d73] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95 flex items-center justify-center gap-2">
                        Validasi Token <i data-lucide="arrow-right" size="20"></i>
                    </button>
                </form>

            </div>

            <p class="text-center text-gray-400 text-xs mt-8">
                &copy; {{ date('Y') }} CBT Al-Ma'soem. Secure Exam System.
            </p>
        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>