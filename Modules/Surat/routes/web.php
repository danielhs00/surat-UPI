<?php

use Illuminate\Support\Facades\Route;
use Modules\Surat\Http\Controllers\SuratController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('surats', SuratController::class)->names('surat');
});
