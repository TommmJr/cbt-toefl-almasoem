<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenUjianController;
use App\Http\Controllers\Admin\UjianController;

// 1. Route Login (PUBLIC)
Route::post('/login', [AuthController::class, 'login']);

// 2. ROUTE TEST ENUM (TANPA AUTH, TANPA CSRF)
Route::post('/test/ujian/section', [UjianController::class, 'storeSection']);

// 3. Route Protected (Butuh Token Login)
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/token/validate', [TokenUjianController::class, 'validateToken']);
    Route::post('/token/generate', [TokenUjianController::class, 'generate']);
});
