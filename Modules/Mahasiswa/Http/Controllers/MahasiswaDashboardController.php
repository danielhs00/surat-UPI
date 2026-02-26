<?php

namespace Modules\Mahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mahasiswa\Models\Template;
use Modules\Mahasiswa\Models\StudentDocument;

class MahasiswaDashboardController extends Controller
{
    public function index(Request $request)
{
    // ================================
    // Ambil semua template yang aktif
    // ================================
    $templates = Template::query()
        ->where('is_active', true)
        ->orderBy('nama_template')
        ->get();


    // ================================
    // Ambil status dari query string
    // contoh URL:
    // /mahasiswa/dashboard?status=converted
    // Kalau tidak ada -> default 'all'
    // ================================
    $status = $request->query('status', 'all');


    // ================================
    // Query dasar: hanya dokumen milik mahasiswa login
    // ================================
    $recentDocsQuery = StudentDocument::where('user_id', auth()->id())
    ->where('hidden_in_dashboard', false);


    // ================================
    // Jika status bukan 'all'
    // maka filter berdasarkan kolom status di database
    // ================================
    if ($status !== 'all') {
        $recentDocsQuery->where('status', $status);
    }


    // ================================
    // Urutkan terbaru & batasi 12 data
    // ================================
    $recentDocs = $recentDocsQuery
        ->orderByDesc('updated_at')
        ->limit(12)
        ->get();


    // ================================
    // Kirim ke view
    // Pastikan di blade memang pakai $status
    // ================================
    return view(
        'mahasiswa::components.dashboard',
        compact('templates', 'recentDocs', 'status')
    );
}
public function clearDashboard(Request $request)
{
    $affected = StudentDocument::where('user_id', auth()->id())
        ->where('hidden_in_dashboard', false)
        ->whereNotIn('status', ['approved_wadek', 'signed', 'signed_by_wadek']) // yang diterima jangan disembunyikan
        ->update(['hidden_in_dashboard' => true]);

    return redirect()->route('mahasiswa.dashboard')
        ->with('success', "Reset daftar berhasil. Disembunyikan: {$affected} dokumen.");
}
}