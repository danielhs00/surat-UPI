<?php

use Illuminate\Support\Facades\Route;
use Modules\Wadek\Http\Controllers\WadekController;

Route::middleware(['auth','role:wadek'])
    ->prefix('wadek')
    ->name('wadek.')
    ->group(function () {

        Route::get('/dashboard', [WadekController::class,'index'])
            ->name('dashboard');

        Route::get('/documents/{document}', [WadekController::class,'show'])
            ->name('documents.show');

        Route::post('/documents/{document}/approve', [WadekController::class,'approve'])
            ->name('documents.approve');

        Route::post('/documents/{document}/reject', [WadekController::class,'reject'])
            ->name('documents.reject');
    });
