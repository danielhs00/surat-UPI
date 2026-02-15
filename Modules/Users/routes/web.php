<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UsersController::class)->names('users');
});

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('users', UsersController::class);
    });

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('users::dashboard');
        })->name('admin.dashboard');
    });



Route::get('/users/operator', [UsersController::class, 'index'])
    ->name('users.operator');
