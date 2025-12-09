@extends('layouts.guest')

@section('title', 'Al Ma\'soem TOEFL CBT System')

@section('content')
<div class="min-h-screen font-sans text-slate-800"
     style="background-image: url('{{ asset('images/sekolahHd.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">

    {{-- Navbar --}}
    <nav class="w-full bg-[#004e92] h-16 flex items-center justify-between px-6 shadow-md fixed top-0 left-0 right-0 z-40">
        <div class="text-white font-semibold text-lg">Al Ma'soem TOEFL CBT System</div>
        <a href="{{ route('login') }}" 
           class="text-white/80 hover:text-white font-medium transition no-underline">
            Login
        </a>
    </nav>

    <div class="mt-24 px-8 pb-20">

        {{-- Hero Section --}}
        <section class="w-full pt-24 px-10">
            <div class="w-full bg-white/90 backdrop-blur-sm rounded-3xl shadow-lg p-10">
                <h1 class="text-4xl font-bold text-[#0066b2] mb-3">
                    SMA Al Ma'soem Bandung
                </h1>
                <p class="text-slate-600 text-sm max-w-2xl mb-5 leading-relaxed">
                    SMA Islam Al Ma'soem adalah institusi pendidikan Islam terkemuka di Jatinangor, Bandung, yang berdedikasi pada pencapaian "Unggul dalam prestasi, berakhlakul karimah dan berdisiplin" sejak tahun 1987. Menawarkan sistem Full Day dan Boarding School dengan fasilitas modern dan lengkap, sekolah ini berfokus pada keseimbangan antara kecerdasan intelektual, moral Islami, dan kedisiplinan tinggi, yang terbukti sukses mengantar rata-rata 58% alumninya lolos ke Perguruan Tinggi Negeri (PTN) favorit setiap tahun.
                </p>

                <a href="https://almasoem.sch.id/profil-sma/" 
                   target="_blank"
                   class="bg-[#0066b2] hover:bg-[#00509e] text-white font-medium px-5 py-2 rounded-xl shadow flex items-center gap-2 transition inline-flex">
                    Selengkapnya <i data-lucide="arrow-right" size="18"></i>
                </a>
            </div>
        </section>

        {{-- 3 Card Menu --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-14 mb-14 w-full max-w-4xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-center mb-3">
                    <i data-lucide="globe" size="32" class="text-[#0066b2]"></i>
                </div>
                <h3 class="text-center font-bold text-base">Online Test</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Ujian berbasis web yang bisa diakses kapan saja.</p>
            </div>

            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-center mb-3">
                    <i data-lucide="monitor" size="32" class="text-[#0066b2]"></i>
                </div>
                <h3 class="text-center font-bold text-base">Computer Based Test</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Simulasi TOEFL berbasis komputer sesuai standar.</p>
            </div>

            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-center mb-3">
                    <i data-lucide="file-text" size="32" class="text-[#0066b2]"></i>
                </div>
                <h3 class="text-center font-bold text-base">Easy to Access</h3>
                <p class="text-center text-gray-500 text-xs mt-1">Login sistem menggunakan token.</p>
            </div>
        </section>

        {{-- Jadwal Ujian --}}
        <section class="bg-white/90 backdrop-blur-sm p-10 rounded-2xl shadow-md mb-20 max-w-5xl mx-auto">
            <h2 class="text-xl font-bold mb-3">Jadwal Tes TOEFL Mendatang</h2>
            <p class="text-slate-600 text-sm mb-5">-----------------------------</p>

            <div class="flex flex-col gap-3">
                @forelse($upcomingUjians ?? $upcomingUjian ?? [] as $ujian)
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-xl flex justify-between items-center text-sm hover:bg-gray-100 transition">
                        {{-- FIX: Ganti tanggal_mulai jadi waktu_mulai --}}
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('d F Y - H:i') }} WIB
                        </span>
                        {{-- FIX: Ganti nama_ujian jadi judul --}}
                        <span class="text-gray-600">{{ $ujian->judul }}</span>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-6">Belum ada jadwal ujian</div>
                @endforelse
            </div>
        </section>

       {{-- Leaderboard --}}
        <section class="bg-white/90 backdrop-blur-sm p-10 rounded-2xl shadow-md max-w-5xl mx-auto mb-20">
            <h2 class="text-xl font-bold mb-3">Leaderboard</h2>
            <p class="text-slate-600 text-sm mb-4">Top score siswa</p>

            <div class="grid grid-cols-3 text-gray-500 text-sm border-b pb-2 font-medium">
                <span>Nama</span>
                <span class="text-center">Kelas</span>
                <span class="text-right">Skor</span>
            </div>

            @forelse($leaderboard ?? [] as $item)
                <div class="grid grid-cols-3 text-sm py-3 px-2 bg-gray-50 rounded-xl mt-3 hover:bg-gray-100 transition">
                    {{-- 
                        FIX: Pake data_get() biar compiler Blade gak pusing baca sintaks array/object.
                        Fungsinya sama: ambil data 'nama' baik dari array maupun object.
                    --}}
                    <span class="font-semibold text-slate-800">
                        {{ data_get($item, 'nama') }}
                    </span>
                    
                    <span class="text-center text-gray-600">
                        {{ data_get($item, 'kelas') }}
                    </span>
                    
                    <span class="text-right font-bold text-slate-800">
                        {{ data_get($item, 'best_score') }}
                    </span>
                </div>
            @empty
                <div class="text-center text-gray-400 py-6">Belum ada data leaderboard</div>
            @endforelse
        </section>

    </div>
</div>
@endsection