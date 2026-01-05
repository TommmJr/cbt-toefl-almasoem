<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\ManajemenUjianController;
use App\Http\Controllers\Guru\SoalController;

Route::prefix('guru')
    ->middleware(['auth', 'role:guru'])
    ->name('guru.')
    ->group(function () {
        
        // Dashboard Utama
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // CRUD Ujian (INI YANG PENTING BANG, SUDAH DIAKTIFKAN)
        Route::resource('ujian', ManajemenUjianController::class);

        // CRUD Soal
        Route::resource('soal', SoalController::class);

        // Route Khusus buat Tambah Section
        Route::post('/ujian/{id}/section', [ManajemenUjianController::class, 'storeSection'])
            ->name('guru.ujian.section.store');

        // Route Form Tambah Soal (Butuh ID Section)
        Route::get('/section/{section}/soal/create', [ManajemenUjianController::class, 'createSoal'])
            ->name('guru.ujian.soal.create');

        // Route Simpan Soal
        Route::post('/section/{section}/soal', [ManajemenUjianController::class, 'storeSoal'])
            ->name('guru.ujian.soal.store');

    });