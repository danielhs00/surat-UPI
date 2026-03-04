<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CasSyncUser
{
    public function handle($request, Closure $next)
    {
        // pastikan cas.auth sudah jalan dulu
        $casUser = session('cas_user') ?? cas()->user(); // cas()->user() tersedia setelah authenticate :contentReference[oaicite:6]{index=6}

        // contoh: anggap casUser adalah username/NIM
        $user = User::firstOrCreate(
            ['username' => $casUser],
            [
                'name' => $casUser,
                'password' => bcrypt(str()->random(32)), // tidak dipakai untuk login biasa
                'role' => 'mahasiswa', // atau mapping sesuai kebutuhanmu
            ]
        );

        Auth::login($user);

        return $next($request);
    }
}