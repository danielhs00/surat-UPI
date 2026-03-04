<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Route API dengan middleware 'auth' (menggunakan session)
Route::get('/prodi/{fakultas}', [App\Http\Controllers\Api\ProdiController::class, 'getByFakultas']);

Route::middleware(['auth'])->group(function () {
    Route::get('/fakultas/{fakultas}/prodi', function($fakultasId) {
        try {
            $prodi = DB::table('prodi')
                ->where('fakultas_id', $fakultasId)
                ->orderBy('nama_prodi')
                ->get(['id', 'nama_prodi']);
            
            return response()->json($prodi);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat data prodi',
                'message' => $e->getMessage()
            ], 500);
        }
    });
});