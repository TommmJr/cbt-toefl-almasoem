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