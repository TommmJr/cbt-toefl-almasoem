<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login TANPA validasi role manual (Otomatis deteksi)
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            // 'role' => 'nullable|in:siswa,guru,admin', // Gak perlu validasi role di request
        ], [
            'username.required' => 'Username/NIS wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $login = $request->username;

        // 1. Cari user (bisa lewat username, email, atau NIS siswa)
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->orWhereHas('siswa', function ($query) use ($login) {
                $query->where('nis', $login);
            })
            ->first();

        if (! $user) {
            return back()->withErrors([
                'username' => 'NIS / Username tidak ditemukan',
            ]);
        }

        // 2. Cek aktif SEBELUM dianggap login sah
        if (! $user->is_active) {
            return back()->withErrors([
                'username' => 'Akun tidak aktif',
            ]);
        }

        // 3. Auth (tanpa role)
        if (! Auth::attempt([
            'username' => $user->username,
            'password' => $request->password,
        ])) {
            return back()->withErrors([
                'password' => 'Password salah',
            ]);
        }

        // 4. Session baru dianggap sah DI SINI
        $request->session()->regenerate();

        // 5. Validasi role DIBUANG SAJA biar gak error salah pilih
        // if ($request->filled('role') && $user->role->value !== $request->role) {
        //     Auth::logout();
        //     return back()->withErrors([
        //         'role' => 'Role tidak sesuai dengan akun',
        //     ]);
        // }

        // 6. Redirect sesuai role (Sistem otomatis tau dia siapa dari database)
        return $this->redirectToDashboard($user->role);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout');
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard($role): RedirectResponse
    {
        // LOGIC FIX:
        // Kalau $role itu String biasa, ya pake langsung.
        $roleName = (is_object($role) && isset($role->value)) ? $role->value : $role;

        return match($roleName) {
            'siswa' => redirect()->route('siswa.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('landing'),
        };
    }
}