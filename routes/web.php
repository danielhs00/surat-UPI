<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\Auth\SsoController;
use Illuminate\Support\Facades\Schema;
use Modules\Users\Http\Controllers\UsersController;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Template\Http\Controllers\TemplateController;
use Modules\Template\Http\Controllers\PengajuanController;
use Modules\Mahasiswa\Models\StudentDocument;
use Modules\Master\Models\Fakultas;

use App\Models\mahasiswa;
use App\Models\operator;
use App\Models\Prodi;

// =========================
// RUTE PUBLIK (TIDAK PERLU LOGIN)
// =========================
// Route untuk mengambil data prodi berdasarkan fakultas (digunakan di dropdown)
Route::get('/publik/prodi/{fakultas}', function ($fakultasId) {
    // Bersihkan output buffer untuk menghindari deprecation warnings
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Matikan error reporting untuk route ini
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Set header JSON
    header('Content-Type: application/json');
    
    try {
        $prodi = DB::table('prodi')
            ->where('fakultas_id', $fakultasId)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi']);
        
        echo json_encode($prodi);
    } catch (\Exception $e) {
        echo json_encode(['error' => 'Gagal memuat data prodi']);
    }
    
    exit;
})->name('publik.prodi.by-fakultas');

// =========================
// RUTE ADMIN (MEMERLUKAN LOGIN & ROLE ADMIN)
// =========================
Route::middleware(['auth', 'role:admin', 'nocache'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [UsersController::class, 'dashboard'])
            ->name('dashboard');

        // Manajemen operator
        Route::get('/operators/{operator}/edit', [UsersController::class, 'editOperator'])
            ->name('operator.edit');

        Route::put('/operators/{operator}/toggle', [UsersController::class, 'toggleOperator'])
            ->name('operator.toggle');
            
        // Simpan operator baru (via modal)
        Route::post('/operators/store', [UsersController::class, 'storeOperator'])
            ->name('operator.store');
    });

/*
    |--------------------------------------------------------------------------
    | Operator
    |--------------------------------------------------------------------------
    */
Route::middleware(['auth', 'role:operator', 'nocache'])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {

        Route::get('/', [TemplateController::class, 'index'])->name('dashboard');

        Route::get('/pengajuan', [PengajuanController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');

        Route::get('/pengajuan/{id}/pdf', [PengajuanController::class, 'viewPdfOperator'])->name('pengajuan.pdf');
        Route::get('/pengajuan/{id}/docx', [PengajuanController::class, 'downloadDocxOperator'])->name('pengajuan.docx');

        Route::put('/pengajuan/{id}/mark-offline', [PengajuanController::class, 'markOffline'])->name('pengajuan.mark_offline');
        Route::put('/pengajuan/{id}/complete', [PengajuanController::class, 'complete'])->name('pengajuan.complete');

        Route::get('/pengajuan/hasil', [PengajuanController::class, 'pengajuanHasil'])
            ->name('pengajuan.hasil');
    });

Route::middleware(['auth', 'role:operator', 'nocache'])
    ->prefix('operator/template')
    ->name('operator.template.')
    ->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/store', [TemplateController::class, 'store'])->name('store');
    });

Route::middleware(['auth', 'role:operator', 'nocache'])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {

        Route::get('/', [TemplateController::class, 'index'])
            ->name('dashboard');
    });

Route::get('/tambah', function () {
    return view('template::operator.templates.tambah');
})->name('tambah');

        /*
    |--------------------------------------------------------------------------
    | router mahasiswa ada di module mahaswa
    |--------------------------------------------------------------------------
    */
