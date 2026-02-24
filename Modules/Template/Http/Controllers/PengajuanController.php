<?php

namespace Modules\Template\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mahasiswa\Models\StudentDocument;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Wadek;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // contoh: operator lihat dokumen yang sudah diupload mahasiswa
        $documents = StudentDocument::with(['user', 'template'])
            ->orderByDesc('updated_at')
            ->get();

        return view('operator::pengajuan.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('template::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('template::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pengajuan = StudentDocument::with(['user.mahasiswa.fakultas', 'template'])->findOrFail($id);
        return view('template::edit', compact('pengajuan'));
    }

    public function destroy($id) {}


    public function pengajuan()
    {
        $op = \App\Models\operator::where('user_id', auth()->id())->first();

        $pengajuans = StudentDocument::with(['user.mahasiswa', 'template'])
            ->when($op, function ($q) use ($op) {
                $q->whereHas('template', function ($t) use ($op) {
                    $t->where('fakultas_id', $op->fakultas_id);
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('template::pengajuan-in', compact('pengajuans'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,uploaded,converting,converted,failed',
            'catatan_operator' => 'nullable|string',
        ]);

        $pengajuan = StudentDocument::findOrFail($id);

        $pengajuan->status = $validated['status'];

        // Kalau kolom catatan_operator belum ada di tabel, komentari 2 baris ini
        if (array_key_exists('catatan_operator', $validated)) {
            $pengajuan->catatan_operator = $validated['catatan_operator'];
        }

        $pengajuan->save();

        return redirect()
            ->route('operator.pengajuan.edit', $pengajuan->id)
            ->with('success', 'Perubahan berhasil disimpan.');
    }

    public function kirimKeWadek($id)
    {
        $pengajuan = StudentDocument::findOrFail($id);

        // aturan sederhana: hanya boleh kirim kalau sudah uploaded/converted
        if (!in_array($pengajuan->status, ['uploaded', 'converted'], true)) {
            return back()->with('error', 'Pengajuan belum siap dikirim ke Wadek.');
        }

        $pengajuan->status = 'sent_to_wadek';

        // opsional: tandai waktu dikirim (pakai submitted_at kalau kamu ingin)
        if (empty($pengajuan->submitted_at)) {
            $pengajuan->submitted_at = now();
        }

        $pengajuan->save();

        return redirect()
            ->route('operator.pengajuan.edit', $pengajuan->id)
            ->with('success', 'Pengajuan berhasil dikirim ke Wadek.');
    }

    public function viewPdf($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'wadek', 403);

        $wdk = wadek::where('user_id', auth()->id())->first();
        abort_unless($wdk, 403, 'Data wadek tidak ditemukan');

        $doc = StudentDocument::with(['template'])
            ->findOrFail($id);

        // ✅ Wadek hanya boleh lihat dokumen fakultasnya
        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && $docFakultasId == $wdk->fakultas_id, 403);

        // ✅ Biasanya wadek hanya lihat yg sudah dikirim ke wadek
        // kalau mau longgar, hapus baris ini
        abort_unless($doc->status === 'sent_to_wadek', 403);

        $path = $doc->pdf_path;
        abort_unless($path, 404, 'PDF belum tersedia');
        abort_unless(Storage::disk('local')->exists($path), 404, 'File PDF tidak ditemukan');

        return response()->file(
            Storage::disk('local')->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dokumen-' . $doc->id . '.pdf"',
            ]
        );
    }

    public function viewPdfOperator($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template'])->findOrFail($id);

        // operator hanya boleh lihat dokumen fakultasnya
        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int)$docFakultasId === (int)$op->fakultas_id, 403);

        // ambil PDF yang sudah dittd dulu, kalau belum ada baru pakai pdf biasa
        $path = $doc->signed_pdf_path ?: $doc->pdf_path;
        abort_unless($path, 404, 'PDF belum tersedia');
        abort_unless(Storage::disk('local')->exists($path), 404, 'File PDF tidak ditemukan');

        return response()->file(
            Storage::disk('local')->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dokumen-' . $doc->id . '.pdf"',
            ]
        );
    }

    public function downloadDocxOperator($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template'])->findOrFail($id);

        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int)$docFakultasId === (int)$op->fakultas_id, 403);

        abort_unless($doc->docx_path, 404, 'DOCX belum tersedia');
        abort_unless(Storage::disk('local')->exists($doc->docx_path), 404, 'File DOCX tidak ditemukan');

        $filename = 'dokumen-' . $doc->id . '.docx';
        return Storage::disk('local')->download($doc->docx_path, $filename);
    }

    public function hasilWadek()
    {
        $pengajuans = \Modules\Mahasiswa\Models\StudentDocument::with(['user.mahasiswa.fakultas', 'template'])
            ->whereIn('status', ['signed', 'rejected', 'signed_by_wadek', 'rejected_by_wadek'])
            ->orderByDesc('updated_at')
            ->get();

        return view('template::pengajuan-hasil', compact('pengajuans'));
    }
}
