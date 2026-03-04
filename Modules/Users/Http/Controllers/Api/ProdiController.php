<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProdiController extends Controller
{
    public function getByFakultas($fakultasId)
    {
        // Matikan semua error output
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Bersihkan output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        try {
            $prodi = DB::table('prodi')
                ->where('fakultas_id', $fakultasId)
                ->orderBy('nama_prodi')
                ->get(['id', 'nama_prodi']);
            
            return response()->json($prodi)
                ->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            Log::error('Error in ProdiController: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat data prodi'
            ], 500)->header('Content-Type', 'application/json');
        }
    }
}