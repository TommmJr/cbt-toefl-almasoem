<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;
// Import Tambahan buat Debug
use App\Models\SesiUjian;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

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
    
    // ... route dashboard & ujian lainnya ...

    Route::resource('ujian', App\Http\Controllers\Guru\ManajemenUjianController::class);
    
    // Route buat Section
    Route::post('/ujian/{id}/section', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'storeSection'])->name('ujian.section.store');
    
    // Route buat Soal
    Route::get('/section/{section}/soal/create', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'createSoal'])->name('ujian.soal.create');
    Route::post('/section/{section}/soal', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'storeSoal'])->name('ujian.soal.store');

    // Route edit Soal
    Route::get('/soal/{soal}/edit', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'editSoal'])->name('ujian.soal.edit');
    Route::put('/soal/{soal}', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'updateSoal'])->name('ujian.soal.update');
    
    // Route delete Soal
    Route::delete('/soal/{id}', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'destroySoal'])->name('ujian.soal.destroy');

    // Route publish Ujian
    Route::put('/ujian/{id}/publish', [App\Http\Controllers\Guru\ManajemenUjianController::class, 'publish'])->name('ujian.publish');
});

/*
|--------------------------------------------------------------------------
| ROUTE DARURAT: DEBUG HANTU (Taruh di paling bawah)
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

        // --- COBA PAKSA UPDATE DISINI ---
        $pesan = "Tidak ada perubahan";
        
        // Logic: Kalau waktu habis DAN statusnya BUKAN selesai -> EKSEKUSI
        if ($isLewat && $statusCurrent !== \App\Enums\StatusUjian::SELESAI) {
            try {
                // Panggil fungsi sakti yang kita buat di Model
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
            'status_awal' => $statusCurrent, // Enum akan otomatis jadi string di JSON response Laravel baru
            'status_sesudah' => $sesi->fresh()->status, 
            'hasil_eksekusi' => $pesan
        ];
    }

    return $report;
});