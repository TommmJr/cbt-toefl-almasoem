<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (siswa, guru, admin)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Cek Login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu');
        }

        $user = Auth::user();

        // 2. FIX: Konversi Enum ke String
        $userRole = (is_object($user->role) && isset($user->role->value)) 
            ? $user->role->value 
            : $user->role;

        // 3. Cek Kesesuaian (Sekarang String vs String, Aman!)
        if (!in_array($userRole, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini. Role Anda: ' . $userRole);
        }

        return $next($request);
    }
}