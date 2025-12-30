<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Ma'soem TOEFL CBT System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 5px;
            background-color: #cbd5e1;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        /* Hover animation */
        .card-hover:hover {
            transform: translateY(-5px);
            transition: 0.3s ease;
        }
    </style>
</head>

<body
    class="font-sans text-slate-800"
    style="background-image: url('{{ asset('images/sekolahHd.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">

  <nav class="w-full bg-[#004e92] h-16 flex items-center justify-between px-6 shadow-md fixed top-0 left-0 right-0 z-40">
        <div class="text-white font-semibold text-lg">Al Ma’soem TOEFL CBT System</div>
        
        @auth
            {{-- KONDISI 1: Kalau User SUDAH LOGIN --}}
            <div class="flex items-center gap-4">
                <span class="text-white/80 text-sm hidden sm:block">
                    Hi, {{ Auth::user()->username }}
                </span>
                
                {{-- Tombol ke Dashboard sesuai Role (Pake value dari Enum) --}}
                <a href="{{ route(Auth::user()->role->value . '.dashboard') }}" 
                   class="bg-white text-[#004e92] hover:bg-gray-100 font-bold px-4 py-2 rounded-lg transition shadow-sm">
                    Ke Dashboard
                </a>
            </div>
        @else
            {{-- KONDISI 2: Kalau User BELUM LOGIN (Tamu) --}}
            <a href="{{ route('login') }}" class="text-white/80 hover:text-white font-medium transition no-underline flex items-center gap-2">
                Login <i data-lucide="log-in" size="18"></i>
            </a>
        @endauth
    </nav>

    <div class="mt-24 px-8 pb-20">

        <section class="w-full pt-24 px-10">
            <div class="w-full bg-white/95 backdrop-blur-sm rounded-3xl shadow-lg p-10">
                <h1 class="text-4xl font-bold text-[#0066b2] mb-3">
                    SMA Al Ma’soem Bandung
                </h1>
                <p class="text-slate-600 text-sm max-w-2xl mb-5 leading-relaxed">
                    SMA Islam Al Ma'soem adalah institusi pendidikan Islam terkemuka di Jatinangor, Bandung, yang berdedikasi pada pencapaian "Unggul dalam prestasi, berakhlakul karimah dan berdisiplin" sejak tahun 1987. Menawarkan sistem Full Day dan Boarding School dengan fasilitas modern dan lengkap, sekolah ini berfokus pada keseimbangan antara kecerdasan intelektual, moral Islami, dan kedisiplinan tinggi, yang terbukti sukses mengantar rata-rata 58% alumninya lolos ke Perguruan Tinggi Negeri (PTN) favorit setiap tahun.
                </p>

                <a href="https://almasoem.sch.id/profil-sma/" target="_blank" class="bg-[#0066b2] hover:bg-[#00509e] text-white font-medium px-5 py-2 rounded-xl shadow flex items-center gap-2 transition inline-flex">
                    Selengkapnya <i data-lucide="arrow-right" size="18"></i>
                </a>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-14 mb-14 w-full max-w-4xl mx-auto">
            <div class="bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-md card-hover">
                <div class="flex justify-center mb-3"><i data-lucide="globe" size="32" class="text-[#0066b2]"></i></div>
                <h3 class="text-center font-bold text-base">Online Test</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Ujian berbasis web yang bisa diakses kapan saja.</p>
            </div>
            <div class="bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-md card-hover">
                <div class="flex justify-center mb-3"><i data-lucide="monitor" size="32" class="text-[#0066b2]"></i></div>
                <h3 class="text-center font-bold text-base">Computer Based Test</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Simulasi TOEFL berbasis komputer sesuai standar.</p>
            </div>
            <div class="bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-md card-hover">
                <div class="flex justify-center mb-3"><i data-lucide="file-text" size="32" class="text-[#0066b2]"></i></div>
                <h3 class="text-center font-bold text-base">Easy to Access</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Login sistem menggunakan token.</p>
            </div>
        </section>

        <section class="bg-white/95 backdrop-blur-sm p-10 rounded-2xl shadow-md mb-20 max-w-5xl mx-auto">
            <h2 class="text-xl font-bold mb-3">Jadwal Tes TOEFL Mendatang</h2>
            <p class="text-slate-600 text-sm">-----------------------------</p>

            <div class="flex flex-col gap-3 mt-5">
                {{-- Loop Data dari Controller --}}
                @forelse($upcomingUjian ?? [] as $ujian)
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-xl flex justify-between items-center text-sm hover:bg-gray-100 transition">
                        {{-- Format Tanggal --}}
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('d F Y - H:i') }} WIB
                        </span>
                        {{-- Nama Ujian --}}
                        <span class="text-gray-600">{{ $ujian->judul }}</span>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-4">Belum ada jadwal ujian</div>
                @endforelse
            </div>
        </section>

        <section class="bg-white/95 backdrop-blur-sm p-10 rounded-2xl shadow-md max-w-5xl mx-auto mb-20">
            <h2 class="text-xl font-bold mb-3">Leaderboard</h2>
            <p class="text-slate-600 text-sm mb-4">Top score siswa</p>

            <div class="grid grid-cols-3 text-gray-500 text-sm border-b pb-2 font-medium">
                <span>Nama</span>
                <span class="text-center">Kelas</span>
                <span class="text-right">Skor</span>
            </div>

            {{-- Loop Leaderboard --}}
            @forelse($leaderboard ?? [] as $item)
                <div class="grid grid-cols-3 text-sm py-3 px-2 bg-gray-50 rounded-xl mt-3 hover:bg-gray-100 transition">
                    {{-- Nama Siswa --}}
                    <span class="font-semibold text-slate-800 truncate">
                        {{ $item->nama }}
                    </span>
                    
                    {{-- Kelas --}}
                    <span class="text-center text-gray-600">
                        {{ $item->kelas }}
                    </span>
                    
                    {{-- Skor --}}
                    <span class="text-right font-bold text-slate-800">
                        {{ $item->best_score }}
                    </span>
                </div>
            @empty
                <div class="text-center text-gray-400 py-6">Belum ada data leaderboard</div>
            @endforelse
        </section>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>