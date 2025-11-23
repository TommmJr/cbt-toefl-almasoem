#!/bin/bash
# Setup Laravel 11 - CBT TOEFL (Linux/Mac - untuk Tommm)

echo "🚀 Memulai setup Laravel 11 CBT TOEFL..."

# 1. Install dependencies tambahan (Skip create-project karena lo udah bikin)
composer require livewire/livewire
composer require maatwebsite/excel
composer require spatie/laravel-permission

# 2. Install frontend dependencies
npm install
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# 3. Setup Livewire
php artisan livewire:publish --config

# 4. Buat folder struktur custom
mkdir -p app/Actions/Ujian
mkdir -p app/Actions/Pengguna
mkdir -p app/Services
mkdir -p app/Enums
mkdir -p storage/app/audio
mkdir -p storage/app/ekspor

# 5. Buat route files terpisah
touch routes/admin.php
touch routes/guru.php
touch routes/siswa.php

# 6. Setup permissions (PENTING BUAT LINUX)
chmod -R 775 storage bootstrap/cache
chmod +x artisan

echo "✅ Setup dasar selesai!"
