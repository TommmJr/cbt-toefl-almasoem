<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
     * Proses login dengan validasi role
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:siswa,guru,admin',
        ], [
            'username.required' => 'Username/NIS wajib diisi',
            'password.required' => 'Password wajib diisi',
            'role.required' => 'Role wajib dipilih',
        ]);

        // 1. Cek Login via USERNAME (Siswa = NIS)
        if (Auth::attempt([
            'username' => $credentials['username'], 
            'password' => $credentials['password'], 
            'role' => $credentials['role']
        ])) {
            $request->session()->regenerate();
            //  Auth::user()->role 
            return $this->redirectToDashboard(Auth::user()->role);
        }

        // 2. Cek Login via EMAIL (Backup)
        if (Auth::attempt([
            'email' => $credentials['username'], 
            'password' => $credentials['password'], 
            'role' => $credentials['role']
        ])) {
            $request->session()->regenerate();
            return $this->redirectToDashboard(Auth::user()->role);
        }

        return back()
            ->withErrors(['username' => 'NIS/Username, password, atau role tidak sesuai'])
            ->withInput($request->except('password'));
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
     * FIX: Hapus type hint 
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