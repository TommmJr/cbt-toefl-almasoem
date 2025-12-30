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
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            $userRole = is_string($user->role)
                ? $user->role
                : $user->role->value;

            if (!in_array($userRole, $roles, true)) {
                // ❗ JANGAN redirect ke route protected
                abort(403, 'Role tidak diizinkan');
            }

            return $next($request);
        }


}