<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;
use App\Models\SesiUjian;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Guru\PenilaianController;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Debug Route (Cek Auth)
|--------------------------------------------------------------------------
*/
Route::get('/debug-auth', function () {
    return [
        'auth_check' => auth()->check(),
        'user_id'    => auth()->id(),
        'user'       => auth()->user(),
    ];
});

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/cek-routing', function () {
    return 'ROUTING HIDUP';
});

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ROLE: SISWA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    
    // Route Dashboard
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    // Route Detail (Halaman sebelum klik mulai)
    Route::get('/ujian/{ujian}/detail', [SiswaUjianController::class, 'detail'])->name('ujian.detail');

    // Route Pengerjaan (Halaman soal)
    Route::get('/ujian/{sesi}/kerjakan', [SiswaUjianController::class, 'show'])->name('ujian.mulai');

    // Nama routenya jadi: siswa.ujian.hasil
    Route::get('/ujian/{sesi}/hasil', [SiswaUjianController::class, 'hasil'])->name('ujian.hasil');
    
    // Alias tambahan biar Dashboard gak bingung (karena tadi abang pake siswa.hasil)
    Route::get('/hasil/{sesi}', [SiswaUjianController::class, 'hasil'])->name('hasil');
});

/*
|--------------------------------------------------------------------------
| ROLE: GURU
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])
        ->name('dashboard');

    // 2. Resource Ujian (CRUD Dasar: index, create, store, show, edit, update, destroy)
    Route::resource('ujian', App\Http\Controllers\Guru\ManajemenUjianController::class);
    
    // 3. Section Routes (Menambah bagian Listening/Reading dll)
    Route::post('/ujian/{id}/section', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'storeSection'])
        ->name('ujian.section.store');
    
    // 4. Soal Routes (CRUD Soal dalam Section)
    Route::get('/section/{section}/soal/create', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'createSoal'])
        ->name('ujian.soal.create');
    Route::post('/section/{section}/soal', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'storeSoal'])
        ->name('ujian.soal.store');
    
    Route::get('/soal/{soal}/edit', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'editSoal'])
        ->name('ujian.soal.edit');
    Route::put('/soal/{soal}', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'updateSoal'])
        ->name('ujian.soal.update');
    Route::delete('/soal/{id}', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'destroySoal'])
        ->name('ujian.soal.destroy');

    // 5. Fitur Ujian (Publish & Start)
    Route::put('/ujian/{id}/publish', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'publish'])
        ->name('ujian.publish');
    Route::put('/ujian/{id}/start', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'start'])
        ->name('ujian.start');
    
    // 6. Siswa Management
    Route::get('/siswa', [App\Http\Controllers\Guru\SiswaController::class, 'index'])
        ->name('siswa.index');
    
    // 7. MANAJEMEN TOKEN SISWA 
    // Generate Token 
    Route::post('/ujian/token/generate', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'generateTokenSiswa'])
        ->name('ujian.generate_token_siswa');

    // Hapus/Reset Token (Pake DELETE by ID Token)
    Route::delete('/ujian/token/{id}', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'hapusTokenSiswa'])
        ->name('ujian.hapus_token_siswa');

   // 8. MANAJEMEN PENILAIAN & ANALISIS 
    Route::prefix('analisis')->name('analisis.')->group(function () {
        // List Semua Ujian (Halaman Index)
        Route::get('/', [PenilaianController::class, 'index'])->name('index');
        
        // List Siswa per Ujian (Halaman Show)
        Route::get('/{ujian}', [PenilaianController::class, 'show'])->name('show');
        
        // Form Koreksi Writing (Logic Lama)
        Route::get('/{ujian}/koreksi/{siswa}', [PenilaianController::class, 'koreksiWriting'])->name('koreksi');
        Route::post('/{ujian}/koreksi/{siswa}', [PenilaianController::class, 'simpanNilaiWriting'])->name('simpan');

        // ==========================================
        //  TAMBAHAN ROUTE AI GRADING WRITING
        // ==========================================
        Route::post('/{ujian}/koreksi/{siswa}/ai-grade', [PenilaianController::class, 'generateAiScore'])
            ->name('ai_grade'); 
    });



});

/*
|--------------------------------------------------------------------------
| ROUTE DARURAT
|--------------------------------------------------------------------------
*/
Route::get('/debug-hantu', function () {
    // 1. Ambil User yang lagi login
    $user = Auth::user();
    if (!$user) return response()->json(['error' => 'Login dulu bang sebagai SISWA!'], 401);

    $siswa = Siswa::where('user_id', $user->id)->first();
    if (!$siswa) return response()->json(['error' => 'Data siswa gak ketemu. Yakin login sebagai siswa?'], 404);

    // 2. Cek semua sesi ujian dia
    $sesiList = SesiUjian::where('siswa_id', $siswa->id)->get();

    if ($sesiList->isEmpty()) {
        return response()->json(['info' => 'Siswa ini belum pernah ikut ujian apapun.'], 200);
    }

    $report = [];
    foreach ($sesiList as $sesi) {
        $now = now();
        $isLewat = $now->greaterThan($sesi->waktu_selesai);
        $statusCurrent = $sesi->status; // Ini Enum Object

        $pesan = "Tidak ada perubahan";
        
        // Logic: Kalau waktu habis DAN statusnya BUKAN selesai -> EKSEKUSI
        if ($isLewat && $statusCurrent !== \App\Enums\StatusUjian::SELESAI) {
            try {
                if (method_exists($sesi, 'hitungNilaiDanSelesai')) {
                    $sesi->hitungNilaiDanSelesai();
                    $pesan = "BERHASIL DIPAKSA SELESAI & HITUNG NILAI";
                } else {
                    $sesi->update(['status' => \App\Enums\StatusUjian::SELESAI]);
                    $pesan = " UPDATE STATUS MANUAL (Method hitungNilaiDanSelesai tidak ditemukan)";
                }
            } catch (\Exception $e) {
                $pesan = " ERROR: " . $e->getMessage();
            }
        }

        $report[] = [
            'id_sesi' => $sesi->id,
            'ujian_id' => $sesi->ujian_id,
            'waktu_sekarang_server' => $now->toDateTimeString(),
            'waktu_selesai_ujian' => $sesi->waktu_selesai ? $sesi->waktu_selesai->toDateTimeString() : 'NULL',
            'apakah_sudah_lewat' => $isLewat ? 'YA' : 'BELUM',
            'status_awal' => $statusCurrent, 
            'status_sesudah' => $sesi->fresh()->status, 
            'hasil_eksekusi' => $pesan
        ];
    }

    return $report;
});

// ROUTE DARURAT BUAT CEK MODEL AI
Route::get('/cek-model-ai', function() {
    $apiKey = config('services.gemini.api_key');
    
    $response = Http::withOptions(['verify' => false]) // Bypass SSL biar gak error di local
        ->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
    
    return $response->json();
});