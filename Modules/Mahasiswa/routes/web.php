<?php

use Illuminate\Support\Facades\Route;
use Modules\Mahasiswa\Http\Controllers\MahasiswaDashboardController;
use Modules\Mahasiswa\Http\Controllers\StudentDocumentController;

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/templates/{template}/download', [StudentDocumentController::class, 'downloadTemplate'])
            ->name('templates.download');

        Route::post('/documents/from-template/{template}', [StudentDocumentController::class, 'createFromTemplate'])
            ->name('documents.fromTemplate');

        Route::post('/documents/{document}/upload-docx', [StudentDocumentController::class, 'uploadDocx'])
            ->name('documents.uploadDocx');

        Route::get('/documents/{document}/download-pdf', [StudentDocumentController::class, 'downloadPdf'])
            ->name('documents.downloadPdf');
    });