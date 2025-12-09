@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

{{-- 4 Card + Button Mulai Ujian --}}
<section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-5 mb-8">
    
    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm text-gray-500">Total Siswa</h3>
        <p class="text-2xl font-bold">{{ $totalSiswa ?? 0 }}</p>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm text-gray-500">Guru/Pengawas</h3>
        <p class="text-2xl font-bold">{{ $totalGuru ?? 0 }}</p>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm text-gray-500">Siswa Mengerjakan</h3>
        <p class="text-2xl font-bold">{{ $siswaAktif ?? 0 }}</p>
    </div>

    {{-- Token Card --}}
    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm text-gray-500">Token</h3>
        <p class="text-2xl font-bold font-mono">
            {{ $currentUjian->token ?? '-' }}
        </p>
    </div>

    {{-- Button Mulai Ujian --}}
    <div class="bg-white rounded-2xl shadow flex">
        <button onclick="openExamModal()"
                class="w-full h-full text-center flex items-center justify-center px-4 py-3 text-white font-bold text-lg rounded-2xl bg-green-600 hover:bg-green-700 transition">
            Mulai Ujian
        </button>
    </div>

</section>

{{-- 2 Panel Tengah --}}
<section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    {{-- Jadwal Tes --}}
    <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-bold mb-4">Jadwal Tes TOEFL Mendatang</h2>
        <div class="space-y-3">
            @forelse($upcomingUjians ?? [] as $ujian)
                <div class="bg-gray-50 border rounded-xl px-4 py-3 flex justify-between text-sm hover:bg-gray-100 transition">
                    <span>{{ \Carbon\Carbon::parse($ujian->tanggal_mulai)->format('d F Y') }}</span>
                    <span class="font-medium text-gray-600">{{ $ujian->nama_ujian }}</span>
                </div>
            @empty
                <p class="text-center text-gray-400 py-6">Belum ada jadwal ujian</p>
            @endforelse
        </div>
    </div>

    {{-- Leaderboard --}}
    <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-bold mb-4">Leaderboard</h2>
        <div class="divide-y">
            <div class="px-2 py-2 grid grid-cols-3 text-sm font-medium text-gray-500">
                <span>Nama</span>
                <span class="text-center">Role</span>
                <span class="text-right">Score</span>
            </div>

            @forelse($leaderboard ?? [] as $item)
                <div class="px-2 py-3 grid grid-cols-3 items-center text-sm bg-gray-50 rounded-xl mb-2 hover:bg-gray-100 transition">
                    <span class="font-semibold">{{ $item['name'] }}</span>
                    <span class="text-center text-gray-600">{{ $item['role'] }}</span>
                    <span class="text-right font-bold">{{ $item['score'] }}</span>
                </div>
            @empty
                <p class="text-center text-gray-400 py-6">Belum ada data</p>
            @endforelse
        </div>
    </div>

</section>

{{-- Recent Activity --}}
<section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-xl font-bold mb-3">Recent Activity</h2>
    <div>
        <div class="grid grid-cols-3 text-sm text-gray-500 font-medium border-b pb-2 px-2">
            <span>Waktu</span>
            <span>User</span>
            <span class="text-right">Aksi</span>
        </div>
        
        @forelse($recentActivities ?? [] as $activity)
            <div class="grid grid-cols-3 text-sm py-3 px-2 bg-gray-50 rounded-xl mt-3 hover:bg-gray-100 transition">
                <span>{{ $activity['time'] }}</span>
                <span>{{ $activity['user'] }}</span>
                <span class="text-right text-gray-700">{{ $activity['action'] }}</span>
            </div>
        @empty
            <p class="text-center text-gray-400 py-6">Belum ada aktivitas</p>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
    function openExamModal() {
        // TODO: Integrate dengan backend untuk mulai ujian
        alert("Fitur mulai ujian akan diintegrasikan dengan backend");
    }
</script>
@endpush
@endsection