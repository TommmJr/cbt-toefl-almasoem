# 🚀 Migration Guide: Refactor ke TokenUjian + UjianSection

**Target**: Migrasi dari struktur lama ke baru tanpa data loss
**Estimated Time**: 2-3 jam (dengan testing)

---

## Phase 1: Backup & Safety (15 menit)

```bash
# 1. Backup database WAJIB!
mysqldump -u root -p cbt_toefl_almasoem > backup_before_refactor.sql

# 2. Commit semua perubahan code
git add .
git commit -m "Pre-refactor checkpoint"
git tag -a v1.0-before-refactor -m "Backup sebelum refactor"

# 3. Buat branch baru untuk refactor
git checkout -b refactor/token-dan-section
```

---

## Phase 2: Database Migration (30 menit)

### Step 1: Jalankan migration baru

```bash
# Copy semua file migration ke database/migrations/

# Jalankan migration
php artisan migrate

# Cek struktur tabel
php artisan db:show
php artisan db:table token_ujian
php artisan db:table ujian_section
```

### Step 2: Data Migration Script

Buat file: `database/seeders/MigrateOldDataSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\{Ujian, UjianSection, Soal};
use App\Enums\TipeSoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateOldDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Untuk setiap ujian yang ada
            Ujian::chunk(100, function ($ujians) {
                foreach ($ujians as $ujian) {
                    // 2. Buat 3 section default (Listening, Structure, Reading)
                    $sections = [
                        [
                            'tipe_section' => TipeSoal::LISTENING,
                            'urutan' => 1,
                            'durasi_menit' => 35,
                            'jumlah_soal_target' => 50,
                        ],
                        [
                            'tipe_section' => TipeSoal::STRUCTURE,
                            'urutan' => 2,
                            'durasi_menit' => 25,
                            'jumlah_soal_target' => 40,
                        ],
                        [
                            'tipe_section' => TipeSoal::READING,
                            'urutan' => 3,
                            'durasi_menit' => 55,
                            'jumlah_soal_target' => 50,
                        ],
                    ];

                    foreach ($sections as $sectionData) {
                        $section = $ujian->sections()->create($sectionData);

                        // 3. Migrate soal lama ke section baru
                        Soal::where('ujian_id', $ujian->id)
                            ->where('tipe_soal', $sectionData['tipe_section']->value)
                            ->update(['ujian_section_id' => $section->id]);
                    }
                }
            });

            $this->command->info('✅ Data migration selesai!');
        });
    }
}
```

Jalankan:
```bash
php artisan db:seed --class=MigrateOldDataSeeder
```

---

## Phase 3: Update Model Relationships (20 menit)

### Update `app/Models/Ujian.php`

Tambahkan relasi baru:

```php
/**
 * Relasi ke ujian sections (1:many)
 */
public function sections(): HasMany
{
    return $this->hasMany(UjianSection::class)->orderBy('urutan');
}

/**
 * Relasi ke token ujian (1:many)
 */
public function tokens(): HasMany
{
    return $this->hasMany(TokenUjian::class);
}

/**
 * Helper: Generate token untuk ujian ini
 */
public function generateToken(User $pembuatToken, int $kuota = 3000): TokenUjian
{
    return TokenUjian::create([
        'ujian_id' => $this->id,
        'kode_token' => TokenUjian::generateKodeToken(),
        'kuota_pemakaian' => $kuota,
        'berlaku_dari' => $this->waktu_mulai->subHour(), // 1 jam sebelum ujian
        'berlaku_sampai' => $this->waktu_selesai,
        'dibuat_oleh' => $pembuatToken->id,
        'ip_address' => request()->ip(),
    ]);
}
```

### Update `app/Models/Soal.php`

Tambahkan:

```php
/**
 * Relasi ke ujian section (many:1)
 */
public function ujianSection(): BelongsTo
{
    return $this->belongsTo(UjianSection::class);
}

/**
 * Helper: Ambil tipe soal dari section (backward compatibility)
 */
public function getTipeSoalAttribute(): TipeSoal
{
    return $this->ujianSection->tipe_section;
}
```

---

## Phase 4: Update Controllers (30 menit)

### Buat Service Class untuk Token

File: `app/Services/TokenUjianService.php`

```php
<?php

namespace App\Services;

use App\Models\{TokenUjian, Siswa};
use Illuminate\Support\Facades\Cache;

class TokenUjianService
{
    /**
     * Validasi token dengan Redis cache (untuk 3000 users)
     */
    public function validasiTokenDenganCache(string $kodeToken, Siswa $siswa): TokenUjian
    {
        // Cache key unik per kombinasi token + siswa
        $cacheKey = "token_validation:{$kodeToken}:{$siswa->id}";

        // Cek cache dulu (TTL 5 menit)
        $cachedResult = Cache::remember($cacheKey, 300, function () use ($kodeToken) {
            return TokenUjian::where('kode_token', $kodeToken)
                ->with('ujian')
                ->aktifDanValid()
                ->first();
        });

        if (!$cachedResult) {
            throw new \Exception('Token tidak valid atau sudah expired');
        }

        // Validasi ulang dengan DB lock (race condition protection)
        $cachedResult->validasiToken($siswa);

        return $cachedResult;
    }
}
```

### Update Controller

File: `app/Http/Controllers/Siswa/UjianController.php`

```php
use App\Services\TokenUjianService;

public function aksesUjian(Request $request, TokenUjianService $tokenService)
{
    $request->validate([
        'kode_token' => 'required|string|size:6|uppercase',
    ]);

    try {
        $siswa = auth()->user()->siswa;
        $token = $tokenService->validasiTokenDenganCache(
            $request->kode_token,
            $siswa
        );

        $sesiUjian = $token->gunakanToken(
            $siswa,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('siswa.ujian.mulai', $sesiUjian->id);

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
```

---

## Phase 5: Testing (45 menit)

### Test 1: Unit Test Token

File: `tests/Unit/TokenUjianTest.php`

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{TokenUjian, Ujian, Siswa};
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenUjianTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_token_unik()
    {
        $token1 = TokenUjian::generateKodeToken();
        $token2 = TokenUjian::generateKodeToken();

        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(6, strlen($token1));
    }

    public function test_validasi_token_expired()
    {
        $token = TokenUjian::factory()->expired()->create();
        $siswa = Siswa::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Token sudah kadaluarsa');

        $token->validasiToken($siswa);
    }
}
```

Jalankan:
```bash
php artisan test --filter=TokenUjianTest
```

### Test 2: Load Test dengan Artisan Command

File: `app/Console/Commands/LoadTestToken.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{TokenUjian, Siswa};
use Illuminate\Support\Facades\DB;

class LoadTestToken extends Command
{
    protected $signature = 'test:token-load {concurrent=100}';

    public function handle()
    {
        $concurrent = (int) $this->argument('concurrent');
        
        $this->info("Testing {$concurrent} concurrent token usage...");

        $token = TokenUjian::factory()->create([
            'kuota_pemakaian' => $concurrent,
        ]);

        $siswaList = Siswa::factory()->count($concurrent)->create();

        $errors = 0;
        $success = 0;

        foreach ($siswaList as $siswa) {
            try {
                $token->gunakanToken($siswa, '127.0.0.1', 'Test Agent');
                $success++;
            } catch (\Exception $e) {
                $errors++;
                $this->error($e->getMessage());
            }
        }

        $this->info("Success: {$success}, Errors: {$errors}");
    }
}
```

Jalankan:
```bash
php artisan test:token-load 100
php artisan test:token-load 1000
```

---

## Phase 6: Rollback Plan (Jika Ada Masalah)

```bash
# 1. Rollback migration
php artisan migrate:rollback --step=2

# 2. Restore backup database
mysql -u root -p cbt_toefl_almasoem < backup_before_refactor.sql

# 3. Kembali ke commit sebelumnya
git checkout v1.0-before-refactor
```

---

## Checklist Final

- [ ] Backup database berhasil
- [ ] Migration berjalan tanpa error
- [ ] Data lama ter-migrate ke structure baru
- [ ] Unit test pass
- [ ] Load test 100 concurrent users: OK
- [ ] Load test 1000 concurrent users: OK
- [ ] Manual test di browser: Bisa akses ujian pakai token
- [ ] Performance check: Query time < 50ms
- [ ] Git commit & push ke remote

---

## Performance Benchmarks

**Target untuk 3000 users**:
- Token generation: < 10ms
- Token validation (with cache): < 5ms
- Token usage (create session): < 100ms
- Query soal per section: < 30ms

**MySQL Configuration** (edit `/etc/mysql/my.cnf`):
```ini
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
max_connections = 500
query_cache_size = 256M
```

**Redis Configuration** (jika pakai Redis):
```bash
# Install Redis
sudo apt install redis-server

# Edit .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```