<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function redirect()
    {
        // sementara dummy (buat testing)
        return redirect()->route('sso.callback', [
            'email' => 'mahasiswa@upi.test',
            'name'  => 'Mahasiswa UPI',
            'is_student' => 1,
        ]);
    }

    public function callback(Request $request)
    {
        /**
         * Kamu akan dapat data dari SSO (contoh struktur).
         * Sesuaikan dengan response asli SSO UPI kamu nanti:
         * - nim
         * - name
         * - email
         * - is_student / status
         */
        $ssoUser = [
            'nim' => $request->input('nim'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'is_student' => $request->boolean('is_student', true),
        ];

        // 1) Tolak kalau bukan mahasiswa
        if (!$ssoUser['is_student']) {
            abort(403, 'Akses ditolak (bukan mahasiswa).');
        }

        // 2) Create / update user (unik pakai email atau nim)
        $user = User::updateOrCreate(
            ['email' => $ssoUser['email']],
            [
                'name' => $ssoUser['name'],
                'role' => 'mahasiswa',
                // password random (tidak dipakai untuk SSO)
                'password' => bcrypt(Str::random(32)),
            ]
        );

        // (Opsional) simpan nim kalau kamu punya kolomnya
        // $user->nim = $ssoUser['nim']; $user->save();

        // 3) Login dan redirect by role
        Auth::login($user);

        return redirect()->route('redirect.after_login');
    }
}
