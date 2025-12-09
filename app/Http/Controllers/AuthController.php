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
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
            'role.required' => 'Role wajib dipilih',
        ]);

        // Coba login dengan email dulu
        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password'], 'role' => $credentials['role']])) {
            $request->session()->regenerate();
            return $this->redirectToDashboard(Auth::user()->role);
        }

        // Jika gagal, coba dengan username (name field)
        if (Auth::attempt(['name' => $credentials['username'], 'password' => $credentials['password'], 'role' => $credentials['role']])) {
            $request->session()->regenerate();
            return $this->redirectToDashboard(Auth::user()->role);
        }

        return back()
            ->withErrors(['username' => 'Username, password, atau role tidak sesuai'])
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
     */
    private function redirectToDashboard(string $role): RedirectResponse
    {
        return match($role) {
            'siswa' => redirect()->route('siswa.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('landing'),
        };
    }
}