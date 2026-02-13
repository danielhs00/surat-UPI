<?php

use Illuminate\Support\Facades\Route;
use Modules\Template\Http\Controllers\TemplateController;

Route::middleware(['auth','role:operator'])
    ->prefix('operator')
    ->group(function () {
        Route::resource('templates', TemplateController::class);
    });

Route::middleware(['auth','role:mahasiswa'])
    ->group(function () {
        Route::get('/templates', [TemplateController::class,'index']);
        Route::get('/templates/{id}/download', [TemplateController::class,'download']);
    });

Route::middleware(['auth','role:operator'])
    ->prefix('operator')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('templates::dashboard');
        })->name('operator.dashboard');
    });