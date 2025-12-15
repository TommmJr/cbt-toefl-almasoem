@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">

    <h1 class="text-2xl font-bold mb-2">Hasil Ujian</h1>

    <h3 class="text-lg text-gray-700 mb-2">
        {{ $sesi->ujian->judul }}
    </h3>

    {{-- STATUS --}}
    <p class="mb-4">
        Status:
        <span class="px-2 py-1 rounded text-sm {{ $sesi->status->badgeColor() }}">
            {{ $sesi->status->label() }}
        </span>
    </p>

    <hr class="my-6">

    {{-- TOTAL SKOR --}}
    <h2 class="text-xl font-semibold">Total Skor Sementara</h2>
    <p class="text-3xl font-bold my-2">{{ $totalSkor }}</p>

    <p class="text-sm text-gray-500 mb-6">
        * Essay belum dinilai. Skor bisa berubah.
    </p>

    <hr class="my-6">

    {{-- HASIL PER SECTION --}}
    @foreach ($sesi->ujian->sections as $section)
        <div class="mb-10">
            <h3 class="text-lg font-semibold mb-3">
                Section: {{ $section->judul_section }}
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">No</th>
                            <th class="border px-2 py-1">Pertanyaan</th>
                            <th class="border px-2 py-1">Jawaban</th>
                            <th class="border px-2 py-1">Kunci</th>
                            <th class="border px-2 py-1">Status</th>
                            <th class="border px-2 py-1">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section->soal as $soal)
                            @php
                                $jawaban = $sesi->jawaban
                                    ->firstWhere('soal_id', $soal->id);
                            @endphp

                            <tr>
                                <td class="border px-2 py-1 text-center">
                                    {{ $soal->nomor_urut }}
                                </td>

                                <td class="border px-2 py-1">
                                    {{ $soal->pertanyaan }}
                                </td>

                                {{-- JAWABAN SISWA --}}
                                <td class="border px-2 py-1">
                                    @if ($jawaban)
                                        {{ $jawaban->jawaban_pilihan ?? $jawaban->jawaban_essay }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- KUNCI JAWABAN --}}
                                <td class="border px-2 py-1 text-center">
                                    {{ $soal->jawaban_benar ?? '-' }}
                                </td>

                                {{-- STATUS JAWABAN --}}
                                <td class="border px-2 py-1 text-center">
                                    @if ($jawaban && $jawaban->is_benar === 1)
                                        <span class="text-green-600 font-semibold">Benar</span>
                                    @elseif ($jawaban && $jawaban->is_benar === 0)
                                        <span class="text-red-600 font-semibold">Salah</span>
                                    @else
                                        <span class="text-gray-500">Menunggu</span>
                                    @endif
                                </td>

                                {{-- SKOR --}}
                                <td class="border px-2 py-1 text-center">
                                    {{ $jawaban->skor ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <a href="{{ route('siswa.dashboard') }}"
       class="inline-block mt-6 text-blue-600 hover:underline">
        ← Kembali ke Dashboard
    </a>

</div>
@endsection
