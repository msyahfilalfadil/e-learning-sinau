<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.

     * Aturan:
     * 1. Admin memiliki hak akses super (bisa melihat dan mengakses semua role).
     * 2. Guru hanya bisa mengakses area guru (dilarang masuk ke siswa atau admin).
     * 3. Siswa hanya bisa mengakses area siswa (dilarang masuk ke guru atau admin).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah terautentikasi (login)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();


        // 2. Admin memiliki akses ke SEMUA role, ATAU jika role user cocok dengan route yang diakses
        if ($user->role === 'admin' || in_array($user->role, $roles)) {
            return $next($request);
        }

        // 3. Jika Siswa mencoba mengakses area lain (Guru atau Admin) -> Blokir & kembalikan ke /siswa
        if ($user->role === 'siswa') {
            return redirect()->route('siswa')->with('error', 'Akses Ditolak: Anda login sebagai Siswa dan dilarang mengakses halaman pengguna lain!');
        }

        // 4. Jika Guru mencoba mengakses area lain (Siswa atau Admin) -> Blokir & kembalikan ke /guru
        if ($user->role === 'guru') {
            return redirect()->route('guru')->with('error', 'Akses Ditolak: Anda login sebagai Guru dan dilarang mengakses halaman pengguna lain!');
        }


        // 5. Fallback jika ada role tak terdefinisi
        abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
    }
}
