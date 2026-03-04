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
use App\Models\wadek;
use Modules\Mahasiswa\Models\StudentDocument;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Dashboard admin (punyamu)
Route::middleware(['auth','role:admin','Nocache'])->get('/admin/dashboard', function () {

    $jumlah_mahasiswa = Mahasiswa::count();
    $jumlah_operator  = Operator::count();
    $jumlah_wadek     = Wadek::count();

    $pengajuans = StudentDocument::with(['user.mahasiswa','template'])
        ->orderByDesc('updated_at')
        ->get();

    return view('users::index', compact(
        'pengajuans',
        'jumlah_mahasiswa',
        'jumlah_operator',
        'jumlah_wadek',
        'pengajuans'
    ));
})->name('admin.dashboard');

// =========================
// RUTE ADMIN (MEMERLUKAN LOGIN & ROLE ADMIN)
// =========================
Route::middleware(['auth', 'role:admin', 'nocache'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'pengajuan'])
            ->name('pengajuan');
    });

// SSO routes (public)
Route::get('/sso/login', [SsoController::class, 'redirect'])->name('sso.login');
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // redirect setelah login (SSO)
    Route::get('/redirect', [RedirectController::class, 'handle'])
        ->name('redirect.after_login');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin', 'nocache'])->get('/admin/dashboard', function () {
        $jumlah_mahasiswa = mahasiswa::count();
        $jumlah_operator  = operator::count();
        $jumlah_wadek     = wadek::count();

        $pengajuans = StudentDocument::with(['user.mahasiswa', 'template'])
            ->orderByDesc('updated_at')
            ->limit(10) // opsional
            ->get();

        return view('users::index', compact(
            'jumlah_mahasiswa',
            'jumlah_operator',
            'jumlah_wadek'
        ));
    })->name('admin.dashboard');
    

    // Admin - Operator
    Route::middleware(['role:admin'])->get('/admin/operator', [UsersController::class, 'operator'])->name('admin.operator');

    Route::get('/admin/operator/tambah-operator', [UsersController::class, 'tambah_operator'])
        ->middleware(['role:admin'])
        ->name('tambah.operator');

    Route::post('/admin/operator/simpan-operator', [UsersController::class, 'storeOperator'])
        ->middleware(['role:admin'])
        ->name('simpan.operator');

    Route::get('/admin/operator/edit/{id}', [UsersController::class, 'editOperator'])
        ->middleware(['role:admin'])
        ->name('edit.operator');

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
        Route::get('/{id}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('destroy');
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
    | Wadek
    |--------------------------------------------------------------------------
    */
    // Route::middleware(['role:wadek'])->get('/wadek/dashboard', function () {
    //     return view('wadek::dashboard'); // atau auth::dashboard
    // });

    Route::middleware(['auth', 'role:wadek', 'nocache'])
        ->prefix('wadek')
        ->name('wadek.')
        ->group(function () {

            Route::get('/dashboard', [WadekDashboardController::class, 'index'])->name('dashboard');

            Route::get('/documents/{id}', [WadekController::class, 'show'])->name('documents.show');

            Route::get('/documents/{id}/pdf', [WadekController::class, 'viewPdf'])->name('documents.pdf');

            Route::put('/documents/{id}/sign', [WadekController::class, 'sign'])->name('documents.sign');

            Route::put('/documents/{id}/reject', [WadekController::class, 'reject'])->name('documents.reject');

            Route::post('/signature', [WadekController::class, 'uploadSignature'])->name('signature.upload');

            Route::get('/documents/{id}/docx', [WadekController::class, 'downloadDocx'])->name('documents.docx');
        });
    
    /*
    |--------------------------------------------------------------------------
    | router mahasiswa ada di module mahaswa
    |--------------------------------------------------------------------------
    */
