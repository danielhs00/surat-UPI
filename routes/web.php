<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // redirect dashboard sesuai role
    Route::get('/', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'wadek' => redirect()->route('wadek.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    })->name('dashboard');

    // MULAI HALAMAN ADMIN
    Route::middleware(['role:admin'])->get('/admin/dashboard', function () {
        return view('users::dashboard');
    })->name('admin.dashboard');
    Route::middleware(['role:admin'])->get('/admin/mahasiswa', function () {
        return view('users::mahasiswa');
    })->name('admin.mahasiswa');
    // SELESAI HALAMAN ADMIN


    Route::middleware(['role:operator'])->get('/operator/dashboard', function () {
        return view('template::dashboard');
    })->name('operator.dashboard');

    Route::middleware(['role:wadek'])->get('/wadek/dashboard', function () {
        return view('auth::dashboard');
    })->name('wadek.dashboard');

    Route::middleware(['role:mahasiswa'])->get('/mahasiswa/dashboard', function () {
        return view('persetujuan::dashboard');
    })->name('mahasiswa.dashboard');
});
