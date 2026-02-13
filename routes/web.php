<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // redirect dashboard sesuai role
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'wadek' => redirect()->route('wadek.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    })->name('dashboard');

    // halaman dashboard masing-masing role
    Route::middleware(['role:admin'])->get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');

    Route::middleware(['role:operator'])->get('/operator/dashboard', function () {
        return view('dashboards.operator');
    })->name('operator.dashboard');

    Route::middleware(['role:wadek'])->get('/wadek/dashboard', function () {
        return view('dashboards.wadek');
    })->name('wadek.dashboard');

    Route::middleware(['role:mahasiswa'])->get('/mahasiswa/dashboard', function () {
        return view('persetujuan::wadek');
    })->name('mahasiswa.dashboard');
});
