<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UsersController::class)->names('users');
});

Route::middleware(['auth','role:operator'])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {

        Route::get('/templates', [\Modules\Template\Http\Controllers\TemplateController::class, 'index'])
            ->name('templates.index');

        Route::get('/templates/create', [\Modules\Template\Http\Controllers\TemplateController::class, 'create'])
            ->name('templates.create');

        Route::post('/templates', [\Modules\Template\Http\Controllers\TemplateController::class, 'store'])
            ->name('templates.store');
    });
