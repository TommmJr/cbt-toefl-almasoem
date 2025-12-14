<?php

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\UjianController;
use Illuminate\Support\Facades\Route;

Route::prefix('siswa')
    ->middleware(['auth', 'role:siswa'])
    ->name('siswa.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard Siswa
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/siswa/cek', function () {
    return 'SISWA ROUTE HIDUP';
});
        /*
        |--------------------------------------------------------------------------
        | Ujian Siswa
        |--------------------------------------------------------------------------
        */

        // LIST UJIAN
        Route::get('/ujian', [UjianController::class, 'index'])
            ->name('ujian.index');

        // FORM INPUT TOKEN (WAJIB DI ATAS)
        Route::get('/ujian/akses', [UjianController::class, 'formToken'])
            ->name('ujian.akses');

        // SUBMIT TOKEN
        Route::post('/ujian/akses', [UjianController::class, 'aksesUjian'])
            ->name('ujian.akses.post');

        // MULAI UJIAN (SESI)
        Route::get('/ujian/mulai/{sesi}', [UjianController::class, 'show'])
            ->name('ujian.mulai');

        // DETAIL UJIAN (PARAMETER TERAKHIR, BIAR GA MAKAN `akses`)
        Route::get('/ujian/{ujian}', [UjianController::class, 'detail'])
            ->name('ujian.detail');
    });
