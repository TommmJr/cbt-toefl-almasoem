# CBT TOEFL Al Ma'soem

Sistem Ujian Berbasis Komputer (**Computer-Based Testing / CBT**) untuk simulasi dan penilaian tes **TOEFL ITP** di Yayasan Al Ma'soem Bandung. Sistem ini dirancang untuk mendukung ribuan peserta ujian secara bersamaan dengan alur pengerjaan berstandar resmi, timer server-side, sistem token dinamis, dan penilaian otomatis menggunakan Google Gemini AI untuk bagian Writing/Essay.

---

## Fitur Utama

### 1. Panel Guru / Pengajar
- **Manajemen Ujian & Section**: Buat, edit, atur durasi, dan kelola sekuens bagian ujian (Listening, Structure, Reading, Writing).
- **Manajemen Soal Lengkap**:
  - Dukungan audio player untuk section *Listening Comprehension*.
  - Dukungan teks bacaan (*passage*) kaya format untuk *Reading*.
  - Opsi jawaban pilihan ganda (A–E) dan soal bertipe *Essay/Writing*.
  - Kustomisasi bobot nilai, batas minimal kata essay, dan kunci jawaban.
- **Manajemen Token Siswa**:
  - Generate token unik 6 digit (alphanumeric) per siswa untuk keamanan akses ujian.
  - Fitur reset token dan pembatasan kuota pemakaian.
- **Analisis Nilai & Koreksi**:
  - Penilaian otomatis TOEFL ITP (konversi skor standar 310–677).
  - **AI-Assisted Grading**: Penilaian essay otomatis dengan feedback komprehensif menggunakan Google Gemini AI.
  - Manual override nilai dan catatan guru.

### 2. Panel Siswa
- **Login NIS / Username**: Akses mudah menggunakan NIS atau username terdaftar.
- **Akses Berbasis Token**: Validasi token sebelum memulai sesi ujian.
- **Antarmuka CBT Modern**:
  - Timer *server-side* anti-refresh (waktu tetap berjalan saat halaman dimuat ulang).
  - Autosave jawaban secara *real-time* saat memilih opsi atau mengetik essay.
  - Navigasi soal dinamis (indikator sudah dijawab / belum).
  - Alur section terkunci (siswa menyelesaikan per section secara berurutan).
  - Auto-submit otomatis jika waktu pengerjaan habis.
- **Dashboard & Riwayat Nilai**: Menampilkan grafik skor, riwayat simulasi, dan detail sertifikat/hasil prediksi TOEFL.

### 3. Keamanan & Integritas Ujian
- Deteksi perpindahan tab browser (*Tab Switch Prevention*).
- Validasi IP address dan browser *User-Agent* per sesi ujian.
- Idempotensi sesi pengerjaan (mencegah duplikasi sesi aktif).

---

##  Tech Stack

- **Backend**: [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Database**: SQLite (Development) / MySQL (Production)
- **Frontend**: Blade Templates, [TailwindCSS](https://tailwindcss.com/), [Livewire](https://livewire.laravel.com/), [Lucide Icons](https://lucide.dev/), [Chart.js](https://www.chartjs.org/)
- **AI Integration**: Google Gemini API via Laravel HTTP Client
- **Authentication**: Laravel Session & Multi-Role Middleware (`admin`, `guru`, `siswa`)


##  Panduan Instalasi Lokal

### 1. Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo`, `sqlite3`, `curl`, `mbstring`, `fileinfo`)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) & NPM

### 2. Clone Repositori
```bash
git clone https://github.com/TommmJr/cbt-toefl-almasoem.git
cd cbt-toefl-almasoem
```

### 3. Install Dependensi
```bash
composer install
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file konfigurasi environment:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database dan API key di dalam file `.env`:
```ini
DB_CONNECTION=sqlite
# Atau jika menggunakan MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=cbt_toefl_almasoem
# DB_USERNAME=root
# DB_PASSWORD=

# Konfigurasi AI Grading (Google Gemini)
GEMINI_API_KEY=your_gemini_api_key_here
```

Jika menggunakan SQLite, pastikan file database tersedia:
```bash
touch database/database.sqlite
```

### 5. Migrasi & Seeder Database
Jalankan migrasi dan isi data dummy:
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 6. Jalankan Aplikasi
Build aset frontend dan jalankan server lokal Laravel:
```bash
npm run build
php artisan serve
```

Aplikasi dapat diakses melalui browser di: `http://localhost:8000`

---

##  Akun Bawaan (Default Seeder)

| Role | Username / NIS | Email | Password |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin@cbt.com` | `123` |
| **Guru** | `gurubahasa` | `guru@cbt.com` | `321` |
| **Siswa 1** | `siswa01` *(NIS: `2025001`)* | `siswa@cbt.com` | `111` |
| **Siswa 2** | `2025002` *(NIS: `2025002`)* | `2025002@cbt.com` | `123` |

---

##  Struktur Direktori Penting

```plaintext
app/
├── Actions/Ujian/           # Business logic ujian (HitungSkor, AutoSubmit)
├── Enums/                   # StatusUjian, TipeSoal, RolePengguna
├── Http/Controllers/
│   ├── Admin/               # Controller modul admin
│   ├── Guru/                # Manajemen ujian, soal, token, penilaian
│   └── Siswa/               # CBT engine, submit jawaban, dashboard
├── Models/                  # Ujian, UjianSection, Soal, SesiUjian, TokenUjian, dll.
└── Services/                # GeminiWritingScorer, TokenUjianService
config/
└── toefl.php                # Konfigurasi standar durasi & tabel skor TOEFL
database/
├── migrations/              # Skema tabel database
└── seeders/                 # Data inisialisasi & dummy TOEFL
resources/views/
├── auth/                    # Halaman login multi-role
├── guru/                    # Tampilan dashboard & manajemen guru
└── siswa/                   # Tampilan CBT siswa & halaman hasil
```

---

##  Menjalankan Unit Testing

Untuk memvalidasi integritas logika token, perhitungan nilai, dan model:
```bash
php artisan test
```

---

##  Lisensi

Proyek ini dikembangkan untuk kebutuhan internal Yayasan Al Ma'soem Bandung.
Lisensi kode di bawah lisensi [MIT](LICENSE).
