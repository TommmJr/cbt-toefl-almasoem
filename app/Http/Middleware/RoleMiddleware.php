<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Support\Facades\Auth;

    class RoleMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next, string $role): Response
        {
            if (!Auth::check()) {
                return redirect('/login');
            }

            $user = Auth::user();

            // Cek apakah Role user sesuai dengan yang diminta halaman
            // pake ->value karena di database enum tersimpan sebagai string
            if ($user->role->value !== $role) {
                // Kalau beda, tendang ke dashboard masing-masing atau 403
                return match($user->role->value) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'guru' => redirect()->route('guru.dashboard'),
                    'siswa' => redirect()->route('siswa.dashboard'),
                    default => abort(403, 'AKSES DITOLAK: Anda tersesat, kawan!'),
                };
            }

            return $next($request);
        }
    }