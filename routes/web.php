<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\Auth\SsoController;

use Modules\Users\Http\Controllers\UsersController;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Template\Http\Controllers\TemplateController;
use Modules\Template\Http\Controllers\PengajuanController;
use Modules\Wadek\Http\Controllers\WadekDashboardController;
use Modules\Wadek\Http\Controllers\WadekController;


use App\Models\mahasiswa;
use App\Models\operator;
use App\Models\wadek;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Dashboard admin (punyamu)
Route::middleware(['auth', 'role:admin', 'nocache'])
    ->get('/admin/dashboard', function () {
        $jumlah_mahasiswa = mahasiswa::count();
        $jumlah_operator  = operator::count();
        $jumlah_wadek     = wadek::count();

        return view('users::index', compact(
            'jumlah_mahasiswa',
            'jumlah_operator',
            'jumlah_wadek'
        ));
    })->name('admin.dashboard');

// Pengajuan admin
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

    Route::put('/admin/operator/update/{id}', [UsersController::class, 'updateOperator'])
        ->middleware(['role:admin'])
        ->name('update.operator');

    Route::delete('/admin/operator/hapus/{id}', [UsersController::class, 'destroyOperator'])
        ->middleware(['role:admin'])
        ->name('hapus.operator');

    // Admin - Wadek
    Route::middleware(['role:admin','nocache'])->get('/admin/wadek', [UsersController::class, 'wadek'])->name('admin.wadek');

    Route::get('/admin/wadek/tambah-wadek', [UsersController::class, 'tambah_wadek'])
        ->middleware(['role:admin'])
        ->name('tambah.wadek');

    Route::post('/admin/wadek/simpan-wadek', [UsersController::class, 'storeWadek'])
        ->middleware(['role:admin'])
        ->name('simpan.wadek');

    Route::get('/admin/wadek/edit/{id}', [UsersController::class, 'editWadek'])
        ->middleware(['role:admin'])
        ->name('edit.wadek');

    Route::put('/admin/wadek/update/{id}', [UsersController::class, 'updateWadek'])
        ->middleware(['role:admin'])
        ->name('update.wadek');

    Route::delete('/admin/wadek/hapus/{id}', [UsersController::class, 'destroyWadek'])
        ->middleware(['role:admin'])
        ->name('hapus.wadek');

    // Logout (punya module Auth)
    Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Operator
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:operator', 'nocache'])
        ->prefix('operator')
        ->group(function () {
            Route::get('/pengajuan', [PengajuanController::class, 'pengajuan'])->name('operator.pengajuan');

            Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('operator.pengajuan.edit');

            Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])->name('operator.pengajuan.update');

            Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('operator.pengajuan.destroy');

            Route::put('/pengajuan/{id}/kirim-wadek', [PengajuanController::class, 'kirimKeWadek'])->name('operator.pengajuan.kirim_wadek');

            Route::get('/operator/pengajuan/{id}/pdf', [PengajuanController::class, 'viewPdfOperator'])->name('operator.pengajuan.pdf');

            Route::get('/pengajuan/hasil', [\Modules\Template\Http\Controllers\PengajuanController::class, 'hasilWadek'])->name('operator.pengajuan.hasil');

            Route::get('/pengajuan/{id}/docx', [PengajuanController::class, 'downloadDocxOperator'])
                ->name('operator.pengajuan.docx');
        });

    Route::middleware(['role:operator'])
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

    Route::middleware(['auth', 'role:operator'])->get('/operator/Template/Tambah', function () {
        return view('template::operator.templates.tambah');
    })->name('template.tambah');

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

});
