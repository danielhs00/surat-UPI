<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;
use Illuminate\Http\Request;

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
    
// Route yang sudah ada
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UsersController::class)->names('users');
});

// ROUTE UNTUK AMBIL PRODI - PASTIKAN INI DITEMPATKAN DI LUAR GROUP MIDDLEWARE APAPUN YANG MENGEMBALIKAN VIEW
Route::get('/admin/fakultas/{fakultas}/prodi', function($fakultasId) {
    // Set header content type ke JSON
    header('Content-Type: application/json');
    
    try {
        // Log untuk debugging
        \Log::info('Mencoba mengambil prodi untuk fakultas ID: ' . $fakultasId);
        
        // Ambil data prodi berdasarkan fakultas_id
        $prodi = DB::table('prodi')
            ->where('fakultas_id', $fakultasId)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi']);
        
        \Log::info('Jumlah prodi ditemukan: ' . $prodi->count());
        
        // Kembalikan response JSON
        return response()->json($prodi);
        
    } catch (\Exception $e) {
        \Log::error('Error loading prodi: ' . $e->getMessage());
        return response()->json([
            'error' => 'Gagal memuat data prodi',
            'message' => $e->getMessage()
        ], 500);
    }
})->name('admin.fakultas.prodi')->withoutMiddleware(['web']); // Tambahkan ini untuk menghindari middleware web

// Route untuk operator (yang sudah ada)
Route::middleware(['auth','role:operator'])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {
        Route::get('/templates', [\Modules\Template\Http\Controllers\TemplateController::class, 'index'])
            ->name('templates.index');
        // ... route lainnya
    });

