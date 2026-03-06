<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function handle(Request $request)
    {
        $role = $request->user()->role;

        return match ($role) {
            'admin'     => redirect()->route('admin.dashboard'),       // sesuaikan
            'operator'  => redirect()->route('operator.dashboard'),    // sesuaikan
            'wadek'     => redirect()->route('wadek.dashboard'),  
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default     => abort(403, 'Role tidak dikenali'),
        };
    }
}
