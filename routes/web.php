<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\Auth\SsoController;

use Modules\Users\Http\Controllers\UsersController;
use Modules\Template\Http\Controllers\TemplateController;
use Modules\Template\Http\Controllers\PengajuanController;

use Modules\Mahasiswa\Models\StudentDocument;
use Modules\Master\Models\Fakultas;

use App\Models\Mahasiswa;
use App\Models\Operator;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// SSO
Route::get('/sso/login', [SsoController::class, 'redirect'])->name('sso.login');
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/redirect', [RedirectController::class, 'handle'])
        ->name('redirect.after_login');

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:admin', 'nocache'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

            Route::get('/dashboard', function () {

                $jumlah_mahasiswa = Mahasiswa::count();
                $jumlah_operator  = Operator::count();

                $pengajuans = StudentDocument::with(['user.mahasiswa', 'template'])
                    ->latest()
                    ->limit(10)
                    ->get();

                $fakultas = Fakultas::all();

                $operators = Operator::with(['user', 'fakultas', 'prodi'])
                    ->latest()
                    ->get();

                return view('users::index', compact(
                    'pengajuans',
                    'jumlah_mahasiswa',
                    'jumlah_operator',
                    'fakultas',
                    'operators'
                ));
            })->name('dashboard');


            /*
        |--------------------------------------------------------------------------
        | Pengajuan Admin
        |--------------------------------------------------------------------------
        */

            Route::get('/pengajuan', [PengajuanController::class, 'pengajuan'])
                ->name('pengajuan');


            /*
        |--------------------------------------------------------------------------
        | Operator CRUD (STANDARD)
        |--------------------------------------------------------------------------
        */

            Route::get('/operator', [UsersController::class, 'operator'])
                ->name('operator.index');

            Route::get('/operator/create', [UsersController::class, 'tambah_operator'])
                ->name('operator.create');

            Route::post('/operator', [UsersController::class, 'storeOperator'])
                ->name('operator.store');

            Route::get('/operator/{id}/edit', [UsersController::class, 'editOperator'])
                ->name('operator.edit');

            Route::put('/operator/{id}', [UsersController::class, 'updateOperator'])
                ->name('operator.update');

            Route::delete('/operator/{id}', [UsersController::class, 'deleteOperator'])
                ->name('operator.destroy');

            Route::put('/operator/{operator}/toggle', [UsersController::class, 'toggleOperator'])
                ->name('operator.toggle');


            /*
        |--------------------------------------------------------------------------
        | JSON Endpoint (Prodi by Fakultas)
        |--------------------------------------------------------------------------
        */

            Route::get('/prodi/by-fakultas/{fakultas_id}', function ($fakultas_id) {

                $prodi = DB::table('prodi')
                    ->where('fakultas_id', $fakultas_id)
                    ->select('id', 'nama_prodi')
                    ->orderBy('nama_prodi')
                    ->get();

                return response()->json($prodi);
            })->name('prodi.byFakultas');
        });


    /*
    |--------------------------------------------------------------------------
    | OPERATOR
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:operator', 'nocache'])
        ->prefix('operator')
        ->name('operator.')
        ->group(function () {

            Route::get('/', [TemplateController::class, 'index'])
                ->name('dashboard');

            Route::get('/pengajuan', [PengajuanController::class, 'pengajuan'])
                ->name('pengajuan');

            Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])
                ->name('pengajuan.edit');

            Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])
                ->name('pengajuan.update');

            Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy'])
                ->name('pengajuan.destroy');

            Route::get('/pengajuan/{id}/pdf', [PengajuanController::class, 'viewPdfOperator'])
                ->name('pengajuan.pdf');

            Route::get('/pengajuan/{id}/docx', [PengajuanController::class, 'downloadDocxOperator'])
                ->name('pengajuan.docx');

            Route::put('/pengajuan/{id}/mark-offline', [PengajuanController::class, 'markOffline'])
                ->name('pengajuan.mark_offline');

            Route::put('/pengajuan/{id}/complete', [PengajuanController::class, 'complete'])
                ->name('pengajuan.complete');

            Route::get('/pengajuan/hasil', [PengajuanController::class, 'pengajuanHasil'])
                ->name('pengajuan.hasil');
        });


    /*
    |--------------------------------------------------------------------------
    | TEMPLATE CRUD
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:operator', 'nocache'])
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
});


/*
|--------------------------------------------------------------------------
| Misc
|--------------------------------------------------------------------------
*/

Route::get('/tambah', function () {
    return view('template::operator.templates.tambah');
})->name('tambah');
