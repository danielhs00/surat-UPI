<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Subfission\Cas\Facades\Cas;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth::index');
    }

    public function startMahasiswaLogin()
    { 
        session(['cas_role' => 'mahasiswa']);
        return redirect()->route('cas.login');
    }
    public function startOperatorLogin()
    {
        return view('auth::login');
    }

    /**
     * LOGIN ADMIN / OPERATOR (email + password)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // optional: remember me
        $remember = (bool) $request->input('remember', false);

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        $role = Auth::user()->role;

        // Bolehkan hanya admin/operator lewat form ini
        if (!in_array($role, ['admin', 'operator'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun ini bukan Admin/Operator.',
            ]);
        }

        // redirect sesuai role
        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            default    => redirect()->route('login'),
        };
    }

public function casLogin(Request $request)
{
    Log::info('CAS: /cas/login hit', ['url' => $request->fullUrl()]);

    $service  = rtrim(config('app.url'), '/') . '/cas/login';
    $casLogin = env('cas_login_url', 'https://sso.upi.edu/cas/login');

    if (!Cas::isAuthenticated()) {
        $redirect = $casLogin . '?service=' . urlencode($service);

        return redirect()->away($redirect);
    }

    $username = Cas::getUser();
    $attrs    = Cas::getAttributes();

    Log::info('CAS: authenticated', ['username' => $username, 'attrs' => $attrs]);

    // ambil attribute
    $email = $attrs['mail'] ?? $attrs['email'] ?? ($username . '@sso.local');

    $nama = 
        $attrs['simpleName'] ??
        $attrs['cn'] ??
        $username;

    $fakultasId = $attrs['fakultas_id'] ?? null;

    // simpan ke database
    $user = \App\Models\User::updateOrCreate(
        ['username' => $username],
        [
            'name'        => $nama,
            'email'       => $email,
            'fakultas_id' => $fakultasId,
        ]
    );

    \Illuminate\Support\Facades\Auth::login($user, true);

    return redirect()->route('mahasiswa.dashboard');
}

  public function destroy(Request $request)
{
    // 1️⃣ Logout Laravel
    Auth::logout();

    // 2️⃣ Hapus session Laravel
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // 3️⃣ Hancurkan session CAS lokal (penting!)
    \Subfission\Cas\Facades\Cas::logout();

    // 4️⃣ Redirect ke CAS logout dan kembali ke halaman login
    return redirect()->away(
        'https://sso.upi.edu/cas/logout?service=' . urlencode(route('login'))
    );
}
}