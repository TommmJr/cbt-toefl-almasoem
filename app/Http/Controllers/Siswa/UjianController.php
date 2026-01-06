<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\SesiUjian;
use App\Models\Jawaban;
use App\Models\Soal; 
use App\Models\Nilai; 
use App\Services\TokenUjianService;
use App\Actions\Ujian\AutoSubmitSectionAction;
use App\Enums\StatusUjian;

class UjianController extends Controller
{
    /**
     * LIST UJIAN
     */
    public function index()
    {
        $ujians = Ujian::published()->with('sections')->get();
        return view('siswa.ujian.index', compact('ujians'));
    }

    /**
     * DETAIL UJIAN
     */
    public function detail(Ujian $ujian)
    {
        return view('siswa.ujian.show', compact('ujian'));
    }

    /**
     * FORM TOKEN
     */
    public function formToken()
    {
        return view('siswa.ujian.akses');
    }

    /**
     * PROSES TOKEN - BUAT / RESUME SESI
     */
    public function aksesUjian(Request $request, TokenUjianService $tokenService)
    {
        $request->validate([
            'kode_token' => 'required|string',
        ]);

        $siswa = auth()->user()->siswa;
        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $token = $tokenService->validasiTokenDenganCache(
            $request->kode_token,
            $siswa
        );

        $sesi = $token->gunakanToken(
            $siswa,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('siswa.ujian.mulai', $sesi->id);
    }

    /**
     * HALAMAN PENGERJAAN CBT
     */
    public function show($sesiId, AutoSubmitSectionAction $autoSubmit)
    {
        $sesi = SesiUjian::with(['ujian.sections.soal', 'jawaban'])
            ->findOrFail($sesiId);

        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);
        $sesi->pastikanBelumSelesai();

        // AUTO SUBMIT (khusus kalau waktu HABIS)
        $autoSubmit->handle($sesi);

        $section = $sesi->sectionAktif();

        // Kalau semua section selesai
        if (! $section && $sesi->semuaSectionSelesai()) {
            $sesi->update([
                'status' => StatusUjian::SELESAI,
                'waktu_selesai' => now(),
            ]);

            return redirect()->route('siswa.hasil', $sesi->id);
        }

        if (! $sesi->section_mulai_at) {
            $sesi->update(['section_mulai_at' => now()]);
        }

        return view('siswa.ujian.kerjakan', [
            'sesi' => $sesi,
            'ujian' => $sesi->ujian,
            'section' => $section,
            'sisaDetik' => $sesi->sisaWaktuSection(),
            'jawabanSiswa' => $sesi->jawaban->keyBy('soal_id'),
        ]);
    }

    /**
     * SUBMIT SECTION (MANUAL VIA BUTTON)
     */
    public function submitSection(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_ujians,id',
        ]);

        $sesi = SesiUjian::findOrFail($request->sesi_id);
        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);

        // Submit section saat ini
        app(AutoSubmitSectionAction::class)->submit($sesi);

        if ($sesi->fresh()->status === StatusUjian::SELESAI) {
            return response()->json([
                'redirect' => route('siswa.hasil', $sesi->id),
            ]);
        }

        return response()->json([
            'redirect' => route('siswa.ujian.mulai', $sesi->id),
        ]);
    }

    /**
     * AUTOSAVE JAWABAN REALTIME DAN SIMPAN
     */
    public function simpanJawaban(
        Request $request,
        AutoSubmitSectionAction $autoSubmit
    ) {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_ujians,id',
            'soal_id' => 'required|exists:soal,id',
        ]);

        $sesi = SesiUjian::findOrFail($request->sesi_id);
        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);

        if ($sesi->isSectionExpired()) {
            return response()->json(['locked' => true, 'message' => 'Waktu habis'], 403);
        }

        $soal = Soal::findOrFail($request->soal_id);

        $dataUpdate = [
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soal->id,
            'waktu_jawab' => now(),
        ];

        if ($soal->tipe_soal === 'writing') {
            $request->validate([
                'jawaban_essay' => 'required|string',
            ]);
            
            $dataUpdate['jawaban_essay'] = $request->jawaban_essay;
            $dataUpdate['jumlah_kata'] = str_word_count(strip_tags($request->jawaban_essay));

        } else {
            $request->validate([
                'jawaban_pilihan' => 'required|string',
            ]);

            $dataUpdate['jawaban_pilihan'] = $request->jawaban_pilihan;
            
            $isBenar = strtoupper((string)$request->jawaban_pilihan) === strtoupper((string)$soal->jawaban_benar);
            
            $dataUpdate['is_benar'] = $isBenar;
            $dataUpdate['skor'] = $isBenar ? $soal->bobot_nilai : 0;
        }

        $jawaban = Jawaban::updateOrCreate(
            [
                'sesi_ujian_id' => $sesi->id,
                'soal_id' => $soal->id,
            ],
            $dataUpdate
        );

        return response()->json([
            'status' => 'saved',
            'jawaban_id' => $jawaban->id,
            'tipe' => $soal->tipe_soal
        ]);
    }

    /**
     * HASIL UJIAN (FIXED: KIRIM DATA NILAI KE VIEW)
     */
    public function hasil($sesiId)
    {
        $sesi = SesiUjian::with(['ujian.sections', 'jawaban.soal'])
            ->findOrFail($sesiId);

        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);

        if ($sesi->status !== StatusUjian::SELESAI) {
            abort(403, 'Ujian belum selesai');
        }

       
        $nilai = Nilai::where('sesi_ujian_id', $sesi->id)->first();

       
        
        return view('siswa.ujian.hasil', [
            'sesi' => $sesi,
            'nilai' => $nilai,
            'totalSkor' => $nilai->skor_total ?? 0,
        ]);
    }
}