<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\SesiUjian;
use App\Models\Jawaban;
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
     * PROSES TOKEN → BUAT / RESUME SESI
     */
    public function aksesUjian(Request $request, TokenUjianService $tokenService)
    {
        $request->validate([
            'kode_token' => 'required|string|size:6|uppercase',
        ]);

       $siswa = auth()->user()->siswa;

        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan. Hubungi admin.');
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
     * HALAMAN PENGERJAAN (INTI CBT)
     */
    public function show($sesiId, AutoSubmitSectionAction $autoSubmit)
    {
        $sesi = SesiUjian::with(['ujian.sections.soal', 'jawaban'])
            ->findOrFail($sesiId);

        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);
        $sesi->pastikanBelumSelesai();

        $autoSubmit->handle($sesi);

        $section = $sesi->sectionAktif();

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
     * SUBMIT SECTION MANUAL
     */
    public function submitSection(Request $request)
    {
    $request->validate([
        'sesi_id' => 'required|exists:sesi_ujians,id',
    ]);

    $sesi = SesiUjian::with('ujian.sections')
        ->findOrFail($request->sesi_id);

    $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);

    app(AutoSubmitSectionAction::class)->submit($sesi);

    $sesi->refresh();

    if ($sesi->masihAdaSection()) {
        return response()->json([
    'status' => 'finished',
    'redirect' => route('siswa.hasil', $sesi->id),
]);

    }

    return response()->json([
        'redirect' => route('siswa.hasil', $sesi->id),
    ]);
    }



/**
 * SIMPAN JAWABAN REALTIME (AUTOSAVE)
 */
    public function simpanJawaban(
        Request $request,
        AutoSubmitSectionAction $autoSubmit
    ) {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_ujians,id',
            'soal_id' => 'required|exists:soal,id',
            'jawaban' => 'nullable|string',
        ]);

        $sesi = SesiUjian::with('ujian.sections.soal')
            ->findOrFail($request->sesi_id);

        $sesi->pastikanBisaDiaksesOleh(auth()->user()->siswa->id);

        // auto submit kalau waktu habis
        $autoSubmit->handle($sesi);

        if ($sesi->isSectionExpired()) {
            return response()->json([
                'status' => 'expired',
                'locked' => true,
            ], 403);
        }

        $section = $sesi->sectionAktif();

        $soal = $section?->soal()
            ->where('id', $request->soal_id)
            ->first();

        if (! $soal) {
            return response()->json([
                'status' => 'invalid_soal',
            ], 403);
        }

        $data = [
            'sesi_ujian_id' => $sesi->id,
            'soal_id'       => $soal->id,
            'waktu_jawab'   => now(),
        ];

        if ($soal->isPilihanGanda()) {
            $data['jawaban_pilihan'] = $request->jawaban;
        } else {
            $data['jawaban_essay'] = $request->jawaban;
        }

        //  INI BARU SIMPAN
        $jawaban = Jawaban::updateOrCreate(
            [
                'sesi_ujian_id' => $sesi->id,
                'soal_id'       => $soal->id,
            ],
            $data
        );

        return response()->json([
            'status'      => 'saved',
            'jawaban_id'  => $jawaban->id,
            'soal_id'     => $soal->id,
            'jawaban'     => $request->jawaban,
        ]);
    }

    /**
     * HASIL UJIAN
     */
        public function hasil($sesiId)
    {
        $sesi = SesiUjian::with([
            'ujian.sections',
            'jawaban.soal',
        ])->findOrFail($sesiId);

        $sesi->pastikanMilikSiswa(auth()->user()->siswa->id);

        if ($sesi->status !== StatusUjian::SELESAI) {
            abort(403, 'Ujian belum selesai');
        }

        return view('siswa.ujian.hasil', [
            'sesi' => $sesi,
            'totalSkor' => $sesi->jawaban->sum('skor'),
            'jawabanPerSection' => $sesi->jawaban
                ->groupBy(fn ($j) => $j->soal->ujian_section_id),
        ]);
}

}
