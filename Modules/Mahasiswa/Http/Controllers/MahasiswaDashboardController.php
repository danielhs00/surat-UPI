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
            ->where('hidden_in_dashboard', 0);


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
            ->where('status', '!=', 'completed')
            ->whereNotIn('status', ['completed', 'rejected'])
            ->update(['hidden_in_dashboard' => 1]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', "Reset daftar berhasil. Disembunyikan: {$affected} dokumen.");
    }

    public function suratSelesai()
    {
        $documents = \Modules\Mahasiswa\Models\StudentDocument::with('template')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['completed', 'rejected'])
            ->orderByDesc('updated_at')
            ->get();

        return view('mahasiswa::surat-selesai', compact('documents'));
    }

    public function resubmit($id)
    {
        $document = \Modules\Mahasiswa\Models\StudentDocument::findOrFail($id);

        // pastikan milik user sendiri
        abort_unless($document->user_id === auth()->id(), 403);

        // hanya boleh jika status rejected
        if ($document->status !== 'rejected') {
            return back()->with('error', 'Dokumen tidak dapat diajukan ulang.');
        }

        // reset status
        $document->status = 'draft'; // atau 'mengupload' sesuai sistem kamu
        $document->catatan_operator = null;
        $document->approved_at = null;
        $document->approved_by = null;

        $document->hidden_in_dashboard = 0;

        $document->save();

        return redirect()
            ->route('mahasiswa.dashboard')
            ->with('success', 'Dokumen berhasil diajukan ulang.');
    }
}
