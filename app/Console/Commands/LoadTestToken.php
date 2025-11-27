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