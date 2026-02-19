<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;
use App\Models\mahasiswa;
use App\Models\operator;
use App\Models\wadek;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Template\Http\Controllers\TemplateController;

Route::middleware(['auth'])->group(function () {

    // redirect dashboard sesuai role
    Route::get('/', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'wadek' => redirect()->route('wadek.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    })->name('dashboard');

    // MULAI HALAMAN ADMIN
    Route::middleware(['role:admin'])->get('/admin/dashboard', function () {
        $jumlah_mahasiswa = Mahasiswa::count();
        $jumlah_operator  = operator::count();
        $jumlah_wadek     = Wadek::count();

    return view('users::index', compact(
        'jumlah_mahasiswa',
        'jumlah_operator',
        'jumlah_wadek'
    ));
    })->name('admin.dashboard');

    // ADMIN OPERATOR
     Route::middleware(['role:admin'])->get('/admin/operator', [UsersController::class, 'operator'])->name('admin.operator');
    // SELESAI ADMIN OPERATOR

    // ADMIN WADEK
     Route::middleware(['role:admin'])->get('/admin/wadek', [UsersController::class, 'wadek'])->name('admin.wadek');
    // SELESAI ADMIN WADEK

    // MULAI TAMBAH OPERATOR
    Route::get('/admin/operator/tambah-operator', [UsersController::class, 'tambah_operator'])
        ->name('tambah.operator');
    Route::post('/admin/operator/simpan-operator', [UsersController::class, 'storeOperator'])
        ->name('simpan.operator');
    // SELESAI TAMBAH OPERATOR

    // mulai edit operator
    Route::get('/admin/operator/edit/{id}', [UsersController::class, 'editOperator'])
        ->name('edit.operator');
    Route::put('/admin/operator/update/{id}', [UsersController::class, 'updateOperator'])
        ->name('update.operator');
    // SELESAI EDIT OPERATOR

    // mulai hapus operator
    Route::delete('/admin/operator/hapus/{id}', [UsersController::class, 'destroyOperator'])
        ->name('hapus.operator');
    // SELESAI HAPUS OPERATOR

    // mulai tambah wadek
    Route::get('/admin/wadek/tambah-wadek', [UsersController::class, 'tambah_wadek'])
        ->name('tambah.wadek');
    Route::post('/admin/wadek/simpan-wadek', [UsersController::class, 'storeWadek'])
        ->name('simpan.wadek');
    // SELESAI TAMBAH WADEK

    // mulai edit wadek
    Route::get('/admin/wadek/edit/{id}', [UsersController::class, 'editWadek'])
        ->name('edit.wadek');
    Route::put('/admin/wadek/update/{id}', [UsersController::class, 'updateWadek'])
        ->name('update.wadek');
    // SELESAI EDIT WADEK

    // mulai hapus wadek
    Route::delete('/admin/wadek/hapus/{id}', [UsersController::class, 'destroyWadek'])
        ->name('hapus.wadek');
    // SELESAI HAPUS WADEK

    //logout
     Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


    // SELESAI HALAMAN ADMIN
    
    // HALAMAN OPERATOR
    Route::middleware(['role:operator'])->get('/operator/pengajuan', [UsersController::class, 'pengajuan'])->name('operator.pengajuan');


    Route::middleware(['auth', 'role:operator'])
    ->prefix('operator/template')
    ->name('operator.template.')
    ->group(function () {

        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/store', [TemplateController::class, 'store'])->name('store');
});

    Route::middleware(['role:operator'])->get('/operator/dashboard', function () {
        return view('template::index');
    })->name('operator.dashboard');

    Route::middleware(['role:operator'])->get('/operator/Template/Tambah', function () {
        return view('template::operator.templates.tambah');
    })->name('template.tambah');

    Route::middleware(['role:wadek'])->get('/wadek/dashboard', function () {
        return view('auth::dashboard');
    })->name('wadek.dashboard');

    Route::middleware(['role:mahasiswa'])->get('/mahasiswa/dashboard', function () {
        return view('persetujuan::dashboard');
    })->name('mahasiswa.dashboard');
});


