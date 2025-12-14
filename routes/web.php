<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

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
| SISWA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::get('/dashboard', [SiswaDashboard::class, 'index'])
        ->name('dashboard');

    // 1. LIST UJIAN
    Route::get('/ujian', [SiswaUjianController::class, 'index'])
        ->name('ujian.index');

    // 2. DETAIL UJIAN
    Route::get('/ujian/{ujian}', [SiswaUjianController::class, 'detail'])
        ->name('ujian.detail');

    // 3. FORM TOKEN
    Route::get('/ujian/akses', [SiswaUjianController::class, 'formToken'])
        ->name('ujian.akses');

    // 4. SUBMIT TOKEN
    Route::post('/ujian/akses', [SiswaUjianController::class, 'aksesUjian'])
        ->name('ujian.akses.post');

    // 5. MULAI UJIAN
    Route::get('/ujian/mulai/{sesi}', [SiswaUjianController::class, 'show'])
        ->name('ujian.mulai');

    // 6. SELESAI UJIAN
    Route::post('/ujian/jawab', [SiswaUjianController::class, 'simpanJawaban'])
        ->name('ujian.jawab');
});

Route::fallback(fn () => '<h1>404</h1>');
