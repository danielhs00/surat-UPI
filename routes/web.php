<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\Auth\SsoController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->get('/redirect', [RedirectController::class, 'handle'])
    ->name('redirect.after_login');

Route::get('/sso/login', [SsoController::class, 'redirect'])->name('sso.login');
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');