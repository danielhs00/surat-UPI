<?php

use Illuminate\Support\Facades\Route;
use Modules\Wadek\Http\Controllers\WadekController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('wadeks', WadekController::class)->names('wadek');
});
