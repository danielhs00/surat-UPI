<?php

use Illuminate\Support\Facades\Route;
use Modules\Surat\Http\Controllers\SuratController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('surats', SuratController::class)->names('surat');
});
