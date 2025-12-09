<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
| Public Routes
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

/*
 Authentication Routes
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*

| Protected Routes (Authenticated Users)
*/

Route::middleware(['auth'])->group(function () {

    // Profile Routes (All roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    | SISWA Routes
    
    */
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');
        
        // TODO: Tambahkan routes untuk:
        // Route::get('/ujian', [SiswaUjianController::class, 'index'])->name('ujian.index');
        // Route::get('/nilai', [SiswaNilaiController::class, 'index'])->name('nilai');
    });

    /*
    | GURU Routes
    */
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');
        
        // Management Soal
        Route::resource('soal', SoalController::class)->except(['show', 'edit', 'update']);
        
        // Route::get('/stats', [GuruDashboard::class, 'stats'])->name('stats');
    });

    /*
     ADMIN Routes
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // Tambahkan routes untuk:
        // Route::resource('ujian', UjianController::class);
        // Route::get('/stats', [AdminDashboard::class, 'stats'])->name('stats');
        // Route::get('/export-nilai', [AdminDashboard::class, 'exportNilai'])->name('export.nilai');
    });

});

/*
 Fallback Route (404)
*/

Route::fallback(function () {
    return view('errors.404');
});