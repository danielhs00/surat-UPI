<?php

use Illuminate\Support\Facades\Route;
use Modules\Persetujuan\Http\Controllers\PersetujuanController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('persetujuans', PersetujuanController::class)->names('persetujuan');
});
