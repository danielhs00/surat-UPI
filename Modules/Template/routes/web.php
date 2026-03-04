<?php

use Illuminate\Support\Facades\Route;
use Modules\Template\Http\Controllers\TemplateController;

// Routes untuk Operator
Route::middleware(['auth','role:operator'])
    ->prefix('operator/template')
    ->name('operator.template.')
    ->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/store', [TemplateController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('destroy');
    });

// Routes untuk Mahasiswa
Route::middleware(['auth','role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {
        Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
        Route::get('/templates/{id}/download', [TemplateController::class, 'download'])->name('templates.download');
    });