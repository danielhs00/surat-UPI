<?php

use Illuminate\Support\Facades\Route;
use Modules\Mahasiswa\Http\Controllers\MahasiswaDashboardController;
use Modules\Mahasiswa\Http\Controllers\StudentDocumentController;

Route::middleware(['auth', 'role:mahasiswa', 'nocache'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/surat-selesai', [MahasiswaDashboardController::class, 'suratSelesai'])
            ->name('surat.selesai');

        Route::get('/templates/{template}/download', [StudentDocumentController::class, 'downloadTemplate'])
            ->name('templates.download');

        Route::post('/documents/from-template/{template}', [StudentDocumentController::class, 'createFromTemplate'])
            ->name('documents.fromTemplate');

        Route::post('/documents/{document}/upload-docx', [StudentDocumentController::class, 'uploadDocx'])
            ->name('documents.uploadDocx');

        Route::get('/documents/{document}/download-pdf', [StudentDocumentController::class, 'downloadPdf'])
            ->name('documents.downloadPdf');
        Route::get('/documents/{id}/pdf', [StudentDocumentController::class, 'viewPdf'])
            ->name('documents.pdf');
        Route::put('/documents/{id}/resubmit', [StudentDocumentController::class, 'resubmit'])->name('documents.resubmit');

        Route::put('/documents/clear-dashboard', [MahasiswaDashboardController::class, 'clearDashboard'])->name('documents.clearDashboard');
    });
