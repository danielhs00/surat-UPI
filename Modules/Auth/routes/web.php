<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('auth::index');
})->name('login');

// tombol mahasiswa
Route::get('/login/mahasiswa', [LoginController::class, 'startMahasiswaLogin'])
    ->name('login.mahasiswa');

// tombol operator
Route::get('/login/operator', [LoginController::class, 'startOperatorLogin'])
    ->name('login.operator');

// login admin & operator (email + password)
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post');

// callback CAS
Route::get('/cas/login', [LoginController::class, 'casLogin'])
    ->name('cas.login');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout');

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('mahasiswa::dashboard');
        })->name('dashboard');
    });

// Debug (opsional)
Route::get('/debug-cas', function () {
    return response()->json([
        'app_url_config' => config('app.url'),
        'cas_service_config' => config('cas.service'),
        'cas_hostname_config' => config('cas.cas_hostname'),
        'cas_uri_config' => config('cas.cas_uri'),
    ]);
});