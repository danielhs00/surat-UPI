<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Subfission\Cas\Facades\Cas;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Modules\Master\Models\Fakultas;

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
            Cas::authenticate();
        }

        $username = Cas::getUser();
        $attrs    = Cas::getAttributes();

        Log::info('CAS: authenticated', ['username' => $username, 'attrs' => $attrs]);

        $email = $attrs['mail'] ?? $attrs['email'] ?? ($username . '@sso.local');
        $nama  = $attrs['simpleName'] ?? $attrs['cn'] ?? $username;

        // default untuk kolom NOT NULL di tabel mahasiswa
        $defaultFakultasId = 1;
        $defaultProdi      = '1';
        $idprodi = 1; // ganti jadi 1 / 0 kalau kolom prodi kamu numeric

        // 1) simpan/update user dulu
        $user = \App\Models\User::updateOrCreate(
            ['username' => $username],
            [
                'name'        => $nama,
                'email'       => $email,
                'fakultas_id' => $defaultFakultasId,
                'prodi'       => $defaultProdi,
                'prodi_id'    => $idprodi,
                'role'        => 'mahasiswa',
            ]
        );

        // 2) cari kode prodi dari CAS (kalau ada)
        $kodeProdi = $attrs['KODEPST']
            ?? $attrs['kodeProdi']
            ?? $attrs['prodi']
            ?? null;

        // ambil record mahasiswa lama (kalau ada)
        $mhsOld = \App\Models\Mahasiswa::where('user_id', $user->id)->first();

        // kalau CAS tidak punya prodi, pakai prodi yg sudah tersimpan
        if (!$kodeProdi && $mhsOld && !empty($mhsOld->prodi)) {
            $kodeProdi = $mhsOld->prodi;
        }

        // prodi final: WAJIB ADA
        $prodiFinal = $kodeProdi ?: $defaultProdi;

        // 3) mapping fakultas dari upi_fjp + upi_fak (kalau punya kode prodi yang valid)
        $mappedFakultasId = null;

        // hanya mapping kalau prodiFinal bukan default "UNKNOWN"
        if ($prodiFinal && $prodiFinal !== $defaultProdi) {
            $rowProdi = DB::table('upi_fjp')->where('KODEPST', $prodiFinal)->first();

            if ($rowProdi && !empty($rowProdi->FAK)) {
                $rowFak = DB::table('upi_fak')->where('FAK', $rowProdi->FAK)->first();

                if ($rowFak) {
                    $fak = \App\Models\Fakultas::firstOrCreate(
                        ['kode' => $rowFak->FAK],
                        ['nama' => $rowFak->NAMAFAK4]
                    );

                    $mappedFakultasId = $fak->id;
                }
            }
        }

        $fakultasIdFinal = $mappedFakultasId ?: $defaultFakultasId;

        // 4) update/create mahasiswa (WAJIB set fakultas_id & prodi)
        \App\Models\Mahasiswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim'         => $username,
                'fakultas_id' => $fakultasIdFinal,
                'prodi'       => $defaultProdi,
                'prodi_id'    => $idprodi,
            ]
        );

        // 5) update user fakultas_id biar konsisten
        $user->update(['fakultas_id' => $fakultasIdFinal]);

        Auth::login($user);

        $request->session()->regenerate();

        Log::info('LOGIN LARAVEL OK', [
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'role' => Auth::user()->role ?? null,
            'session_id' => $request->session()->getId(),
        ]);

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
