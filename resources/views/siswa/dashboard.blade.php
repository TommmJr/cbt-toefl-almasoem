@extends('layouts.dashboard')

@section('title', 'Dashboard Siswa')

@section('content')
<header class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
    <div class="bg-white px-4 py-2 rounded-xl shadow text-gray-700">
        {{ now()->format('F Y') }}
    </div>
</header>

{{-- Stats Cards --}}
<section class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm font-medium text-gray-500">Total Attempts</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $totalAttempts ?? 0 }}</p>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm font-medium text-gray-500">Reading Average</h3>
        <p class="text-2xl font-bold text-gray-800">
            {{ $readingAvg ? number_format($readingAvg, 1) : 'N/A' }}
        </p>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm font-medium text-gray-500">Listening Average</h3>
        <p class="text-2xl font-bold text-gray-800">
            {{ $listeningAvg ? number_format($listeningAvg, 1) : 'N/A' }}
        </p>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow hover:shadow-md transition">
        <h3 class="text-sm font-medium text-gray-500">Writing Average</h3>
        <p class="text-2xl font-bold text-gray-800">
            {{ $writingAvg ? number_format($writingAvg, 1) : 'N/A' }}
        </p>
    </div>
</section>

{{-- Charts Section --}}
<section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-5 rounded-2xl shadow">
        <h2 class="font-bold text-gray-700 mb-4">Score Distribution</h2>
        <div class="flex justify-center items-center h-48 text-gray-400">
            <p>Chart akan ditampilkan di sini</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow">
        <h2 class="font-bold text-gray-700 mb-4">Score Trends</h2>
        <div class="flex justify-center items-center h-48 text-gray-400">
            <p>No data available</p>
        </div>
    </div>
</section>

{{-- Recent Activity --}}
<section class="bg-white p-5 rounded-2xl shadow">
    <h2 class="font-bold text-gray-700 mb-4">Recent Activity</h2>
    
    @if(isset($recentNilais) && $recentNilais->count() > 0)
        <div class="divide-y">
            @foreach($recentNilais as $nilai)
                <div class="py-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800">{{ $nilai->ujian->nama_ujian ?? 'Ujian' }}</p>
                        <p class="text-xs text-gray-500">{{ $nilai->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-blue-600">{{ $nilai->total_score }}</p>
                        <p class="text-xs text-gray-500">Total Score</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-400 py-10">No activity found</div>
    @endif
</section>

<footer class="text-center mt-8 text-gray-500 text-sm">
    Login sebagai: <strong class="text-gray-700">{{ auth()->user()->name }}</strong>
</footer>
@endsection