<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Siswa;
use App\Models\Jawaban;
use App\Models\SesiUjian; 
use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    public function index()
    {
        $ujian = Ujian::query()
            ->withCount(['sesiUjian as total_peserta']) 
            ->latest()
            ->paginate(10);

        return view('guru.penilaian.index', compact('ujian'));
    }

    public function show($id)
    {
        $ujian = Ujian::findOrFail($id);
        
        $siswas = Siswa::whereHas('sesiUjian', function($q) use ($id) {
            $q->where('ujian_id', $id);
        })->with(['user', 'nilai' => function($q) use ($id) {
            $q->where('ujian_id', $id);
        }])->get();

        return view('guru.penilaian.show', compact('ujian', 'siswas'));
    }

    /**
     * 3. Form Koreksi Writing 
     */
    public function koreksiWriting($ujian_id, $siswa_id)
    {
        $ujian = Ujian::findOrFail($ujian_id);
        $siswa = Siswa::findOrFail($siswa_id);

        // 1. Cari dulu Sesi Ujian si siswa di ujian ini
        $sesi = SesiUjian::where('ujian_id', $ujian_id)
            ->where('siswa_id', $siswa_id)
            ->firstOrFail(); // Error 404 kalau siswa belum pernah ikut ujian

        // 2. Ambil Jawaban berdasarkan sesi_ujian_id 
        $jawabanWriting = Jawaban::where('sesi_ujian_id', $sesi->id)
            ->whereHas('soal.ujianSection', function($q) { 
                $q->where('tipe_section', 'writing');
            })
            ->with('soal') 
            ->get();

        return view('guru.penilaian.writing', compact('ujian', 'siswa', 'jawabanWriting'));
    }

    /**
     * 4. Simpan Nilai Writing 
     */
   public function simpanNilaiWriting(Request $request, $ujian_id, $siswa_id)
    {
        $request->validate([
            'nilai' => 'required|array', 
            'nilai.*' => 'numeric|min:0|max:100', 
        ]);

        // 1. Cari Sesi Ujian 
        $sesi = SesiUjian::where('ujian_id', $ujian_id)
            ->where('siswa_id', $siswa_id)
            ->firstOrFail();

        // 2. Simpan Jawaban & Hitung Total Writing
        $totalSkorWriting = 0;
        
        foreach ($request->nilai as $soal_id => $skor) {
            // Update jawaban berdasarkan sesi_ujian_id 
            $jawaban = Jawaban::where('sesi_ujian_id', $sesi->id)
                        ->where('soal_id', $soal_id)
                        ->first();
            
            if ($jawaban) {
                $jawaban->update([
                    'skor' => $skor,
                    'teacher_score' => $skor,
                    'is_reviewed_by_teacher' => true
                ]);

                $totalSkorWriting += $skor;
            }
        }

        // 3. UPDATE ATAU BUAT NILAI (Pakai sesi_ujian_id biar gak double)
        $nilai = Nilai::firstOrNew(['sesi_ujian_id' => $sesi->id]);

        // Isi data pelengkap kalau ini data baru
        $nilai->ujian_id = $ujian_id;
        $nilai->siswa_id = $siswa_id;
        
        // Ambil nilai lama section lain (biar gak ke-reset jadi 0)
        $listening = $nilai->skor_listening ?? 0;
        $structure = $nilai->skor_structure ?? 0; 
        $reading   = $nilai->skor_reading ?? 0;
        
        // Update Skor Writing Baru
        $nilai->skor_writing = $totalSkorWriting;

        // Hitung Total Skor Baru
        $nilai->skor_total = $listening + $structure + $reading + $totalSkorWriting;

        // Simpan Detik Pengerjaan 
        if (!$nilai->durasi_pengerjaan_detik) {
            $nilai->durasi_pengerjaan_detik = $sesi->sisa_waktu ? (7200 - $sesi->sisa_waktu) : 0; 
        }

        $nilai->save(); 

        return redirect()->route('guru.analisis.show', $ujian_id)
            ->with('success', "Disimpan! Writing: $totalSkorWriting, Total: {$nilai->skor_total}");
    }
}