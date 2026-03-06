<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SsoController extends Controller
{
    // STEP 1: arahkan ke SSO (sementara dummy)
    public function redirect()
    {
        // Nanti ini diganti URL SSO asli
        // Untuk sekarang langsung panggil callback dummy
        return redirect()->route('sso.callback', [
            'email' => 'mahasiswa@upi.test',
            'name' => 'Mahasiswa UPI',
        ]);
    }

    // STEP 2: callback menerima data dari SSO
    public function callback(Request $request)
    {
        // Nanti diganti dengan data asli dari SSO
        $email = $request->input('email');
        $name  = $request->input('name');

        if (!$email) {
            abort(403, 'SSO gagal.');
        }

        // Buat atau update user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name ?? 'Mahasiswa',
                'role' => 'mahasiswa',
                'password' => bcrypt(Str::random(32)), // tidak dipakai
            ]
        );

        // Login-kan user
        Auth::login($user);

        // Redirect berdasarkan role
        return redirect()->route('redirect.after_login');
    }
}