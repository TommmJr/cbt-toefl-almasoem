<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Siswa;
use App\Models\Jawaban;
use App\Models\Nilai;

class PenilaianController extends Controller
{
    // 1. Tampilkan Halaman Koreksi Writing per Siswa
    public function koreksiWriting($ujian_id, $siswa_id)
    {
        $ujian = Ujian::findOrFail($ujian_id);
        $siswa = Siswa::findOrFail($siswa_id);

        // Ambil Jawaban Writing Siswa
        $jawabanWriting = Jawaban::where('siswa_id', $siswa_id)
            ->whereHas('soal.section', function($q) use ($ujian_id) {
                $q->where('ujian_id', $ujian_id)
                  ->where('tipe_section', 'writing');
            })
            ->with('soal') 
            ->get();

        return view('guru.penilaian.writing', compact('ujian', 'siswa', 'jawabanWriting'));
    }

    // 2. Simpan Nilai Writing
    public function simpanNilaiWriting(Request $request, $ujian_id, $siswa_id)
    {
        $request->validate([
            'nilai' => 'required|array', 
            'nilai.*' => 'numeric|min:0|max:100', 
        ]);

        foreach ($request->nilai as $soal_id => $skor) {
            // Update skor di tabel Jawaban
            // (Atau kalau lu nyimpen nilai section terpisah, sesuaikan disini)
            $jawaban = Jawaban::where('siswa_id', $siswa_id)
                        ->where('soal_id', $soal_id)
                        ->first();
            
            if ($jawaban) {
                $jawaban->skor = $skor;
                $jawaban->is_ragu = false; 
                $jawaban->save();
            }
        }

        return redirect()->route('guru.ujian.show', $ujian_id)
            ->with('success', 'Nilai Writing berhasil disimpan!');
    }
}