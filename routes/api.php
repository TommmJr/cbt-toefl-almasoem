<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenUjianController;

// 1. Route Login (PUBLIC - Gak butuh token)
Route::post('/login', [AuthController::class, 'login']);

// 2. Route Protected (Butuh Token Login)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Auth Stuff
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Validasi Token Ujian (Siswa)
    Route::post('/token/validate', [TokenUjianController::class, 'validateToken']);
    
    // Generate Token (Guru)
    Route::post('/token/generate', [TokenUjianController::class, 'generate']);
});
