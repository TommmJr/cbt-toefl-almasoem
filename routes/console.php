<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jalanin update leaderboard tiap 10 menit, tapi cuma pas jam kerja (7 pagi - 5 sore)
Schedule::command('leaderboard:update')
    ->everyMinute();
    //->between('07:00', '17:00')
    //->withoutOverlapping(); // Kalau proses sebelumnya belum kelar, jangan ditumpuk

// Bersihin sampah cache tiap jam 2 pagi
//Schedule::command('cache:clear')->dailyAt('02:00');