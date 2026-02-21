<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth::login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // OPTIONAL (rekomendasi): blok mahasiswa login via form biasa
            // karena mahasiswa nanti wajib SSO
            if (auth()->user()->role === 'mahasiswa') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Mahasiswa wajib login lewat SSO.',
                ]);
            }

            // INI KUNCI: semua login sukses lewat pintu redirect role
            return redirect()->route('redirect.after_login');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);

        if (auth()->user()->role === 'mahasiswa') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Mahasiswa wajib login lewat SSO.',
            ]);
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
