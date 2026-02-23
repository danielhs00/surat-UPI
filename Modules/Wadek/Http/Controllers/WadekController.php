<?php

namespace Modules\Wadek\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mahasiswa\Models\StudentDocument;
use Illuminate\Support\Facades\Storage;
use App\Models\Wadek;

class WadekController extends Controller
{
    public function index()
    {
        $documents = StudentDocument::where('status', 'verified_operator')
            ->orderByDesc('updated_at')
            ->get();

        return view('wadek::dashboard', compact('documents'));
    }

    public function show(StudentDocument $document)
    {
        return view('wadek::show', compact('document'));
    }

    private function generateNomorSurat(): string
    {
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $bulan = $bulanRomawi[now()->month];
        $tahun = now()->year;

        // hitung jumlah surat yang sudah approved tahun ini
        $count = StudentDocument::whereYear('approved_at', $tahun)->count() + 1;

        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$urutan}/UPI/{$bulan}/{$tahun}";
    }

    public function reject(Request $request, StudentDocument $document)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $document->update([
            'status' => 'approved_wadek',
            'nomor_surat' => $nomor,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Surat ditolak.');
    }

    public function viewPdf($id)
    {
        $document = StudentDocument::with(['template'])->findOrFail($id);

        // Wadek login
        $wdk = wadek::where('user_id', auth()->id())->firstOrFail();

        // Batasi: hanya dokumen fakultas wadek + status sudah dikirim
        abort_unless($document->status === 'sent_to_wadek', 403, 'Dokumen belum dikirim ke wadek.');

        abort_unless(
            optional($document->template)->fakultas_id == $wdk->fakultas_id,
            403,
            'Akses ditolak.'
        );

        abort_unless(!empty($document->pdf_path), 404, 'PDF belum tersedia.');

        // Pastikan pakai disk yang benar: local (storage/app)
        abort_unless(Storage::disk('local')->exists($document->pdf_path), 404, 'File PDF tidak ditemukan.');

        return Storage::disk('local')->response($document->pdf_path);
        // atau download:
        // return Storage::disk('local')->download($document->pdf_path);
    }
}
