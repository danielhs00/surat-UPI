<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;
use App\Models\mahasiswa;
use App\Models\operator;
use App\Models\wadek;

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

    Route::middleware(['role:admin'])->get('/admin/mahasiswa', [UsersController::class, 'mahasiswa'])->name('admin.mahasiswa');

     Route::middleware(['role:admin'])->get('/admin/operator', [UsersController::class, 'operator'])->name('admin.operator');

     Route::middleware(['role:admin'])->get('/admin/wadek', [UsersController::class, 'wadek'])->name('admin.wadek');
     
    // mulai tambah mahasiswa
    Route::middleware(['role:admin'])->get('/admin/mahasiswa/tambah-mahasiswa', [UsersController::class, 'tambah_mahasiswa'])->name('tambah.mahasiswa');

    Route::post('admin/mahasiswa/simpan', [UsersController::class, 'store'])
        ->name('simpan.mahasiswa');

    // SELESAI TAMBAH MAHASISWA

    // mulai edit mahasiswa
    Route::get('admin/mahasiswa/edit/{id}', [UsersController::class, 'editMahasiswa'])
        ->name('edit.mahasiswa');
    Route::put('admin/mahasiswa/update/{id}', [UsersController::class, 'updateMahasiswa'])
        ->name('update.mahasiswa');

    // SELESAI EDIT MAHASISWA

    // mulai hapus mahasiswa
    Route::delete('admin/mahasiswa/hapus/{id}', [UsersController::class, 'destroyMahasiswa'])
        ->name('hapus.mahasiswa');
    // SELESAI HAPUS MAHASISWA

    // mulai tambah operator
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


    // SELESAI HALAMAN ADMIN


    Route::middleware(['role:operator'])->get('/operator/dashboard', function () {
        return view('template::index');
    })->name('operator.dashboard');

    Route::middleware(['role:wadek'])->get('/wadek/dashboard', function () {
        return view('auth::dashboard');
    })->name('wadek.dashboard');

    Route::middleware(['role:mahasiswa'])->get('/mahasiswa/dashboard', function () {
        return view('persetujuan::dashboard');
    })->name('mahasiswa.dashboard');
});
