#!/bin/bash
# Script untuk testing migration (Linux/Mac)

echo "🧪 Testing Database Migration..."

# 1. Fresh migration (hapus semua tabel lalu migrate ulang)
php artisan migrate:fresh

# 2. Cek status migration
php artisan migrate:status

# 3. (Optional) Seed sample data
# php artisan db:seed

echo "✅ Migration test selesai!"
echo "📊 Cek tabel di database untuk verifikasi"
