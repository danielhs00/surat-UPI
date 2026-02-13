<?php

use Illuminate\Support\Facades\Route;
use Modules\Persetujuan\Http\Controllers\PersetujuanController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('persetujuans', PersetujuanController::class)->names('persetujuan');
});

Route::middleware(['auth','role:wadek'])
    ->prefix('wadek')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('submissions::wadek.dashboard');
        })->name('wadek.dashboard');
    });