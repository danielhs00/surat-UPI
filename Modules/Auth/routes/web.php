<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\LoginController;



// Login & Logout
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');

// Auth Module (protected)
Route::middleware('web')
->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/', [LoginController::class, 'store'])->name('login.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

route::middleware('auth')->group(function () {
    // Tambahkan rute yang memerlukan autentikasi di sini
    // Contoh: Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});