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
            ->orderByDesc('approved_at')
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
    public function store(Request $request)
    {
    }

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
        $pengajuan = StudentDocument::with(['user.mahasiswa', 'template'])->findOrFail($id);

        return view('template::pengajuan.edit', compact('pengajuan'));
    }

    public function destroy($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template', 'versions'])->findOrFail($id);

        // operator hanya boleh hapus dokumen fakultasnya
        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int) $docFakultasId === (int) $op->fakultas_id, 403);

        $disk = Storage::disk('local');

        // hapus file utama kalau ada
        foreach (['docx_path', 'pdf_path', 'signed_pdf_path'] as $field) {
            $path = $doc->{$field} ?? null;
            if ($path && $disk->exists($path)) {
                $disk->delete($path);
            }
        }

        // hapus file versi (kalau kamu punya relasi versions)
        if (method_exists($doc, 'versions')) {
            foreach ($doc->versions as $v) {
                if (!empty($v->docx_path) && $disk->exists($v->docx_path))
                    $disk->delete($v->docx_path);
                if (!empty($v->pdf_path) && $disk->exists($v->pdf_path))
                    $disk->delete($v->pdf_path);
            }
            $doc->versions()->delete();
        }

        // hapus folder dokumen user/id kalau kamu simpan di sana (opsional tapi rapi)
        $disk->deleteDirectory('mahasiswa/documents/' . $doc->user_id);

        $doc->delete();

        return redirect()
            ->route('operator.pengajuan')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }


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
        $pengajuan = StudentDocument::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,mengupload,converting,converted,gagal,submitted,processing_offline,completed,rejected',
            'catatan_operator' => 'nullable|string',
        ]);

        // ✅ cegah completed kalau belum ada pdf final
        if ($validated['status'] === 'completed' && empty($pengajuan->signed_pdf_path)) {
            return back()->with('error', 'Tidak bisa set completed sebelum upload PDF final.');
        }

        $pengajuan->status = $validated['status'];

        // kolom ini ada di tabel kamu, jadi aman
        $pengajuan->catatan_operator = $validated['catatan_operator'] ?? null;

        $pengajuan->save();

        return redirect()
            ->route('operator.pengajuan.edit', $pengajuan->id)
            ->with('success', 'Perubahan berhasil disimpan.');
    }
    public function markOffline($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template'])->findOrFail($id);

        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int) $docFakultasId === (int) $op->fakultas_id, 403);

        $allowedStatuses = ['mengupload', 'converting', 'converted', 'submitted'];

        if (!in_array($doc->status, $allowedStatuses, true)) {
            return back()->with('error', 'Status saat ini: ' . $doc->status . '. Dokumen belum siap diproses offline.');
        }

        $doc->status = 'processing_offline';

        if (empty($doc->submitted_at)) {
            $doc->submitted_at = now();
        }

        $doc->save();

        return redirect()
            ->route('operator.pengajuan.edit', $doc->id)
            ->with('success', 'Ditandai: Diproses Offline.');
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

    public function complete(Request $request, $id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template'])->findOrFail($id);

        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int) $docFakultasId === (int) $op->fakultas_id, 403);

        $allowed = [
            'mengupload',
            'converting',
            'converted',
            'submitted',
            'processing_offline'
        ];

        abort_unless(in_array($doc->status, $allowed, true), 403, 'Dokumen belum disiapkan.');

        $validated = $request->validate([
            'signed_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,mengupload,converting,converted,gagal,submitted,processing_offline,completed,rejected',
            'catatan_operator' => 'nullable|string',
            'nomor_surat' => 'nullable|string|max:255',
        ]);

        // default pakai file lama jika tidak upload baru
        $path = $doc->signed_pdf_path;

        // kalau upload file baru, simpan file baru
        if ($request->hasFile('signed_pdf')) {
            $path = $request->file('signed_pdf')->store('signed_pdfs', 'local');
        }

        $doc->catatan_operator = $validated['catatan_operator'] ?? null;
        $doc->nomor_surat = $validated['nomor_surat'] ?? null;
        $doc->signed_pdf_path = $path;

        // kalau upload PDF final, otomatis completed
        if ($request->hasFile('signed_pdf')) {
            if (empty($validated['nomor_surat'])) {
                return back()
                    ->withErrors(['nomor_surat' => 'Nomor surat wajib diisi jika upload PDF final.'])
                    ->withInput();
            }

            $doc->status = 'completed';
            $doc->approved_at = now();
            $doc->approved_by = auth()->id();
            $doc->hidden_in_dashboard = 0;
        } else {
            // kalau tidak upload file, ikuti status dari form
            $doc->status = $validated['status'];
        }

        // cegah completed jika belum ada pdf final sama sekali
        if ($doc->status === 'completed' && empty($doc->signed_pdf_path)) {
            return back()->with('error', 'Tidak bisa set completed sebelum upload PDF final.');
        }

        $doc->save();

        return redirect()
            ->route('operator.pengajuan.edit', $doc->id)
            ->with('success', 'Perubahan berhasil disimpan.');
    }

    public function viewPdfOperator($id)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'operator', 403);

        $op = \App\Models\operator::where('user_id', auth()->id())->first();
        abort_unless($op, 403, 'Data operator tidak ditemukan');

        $doc = StudentDocument::with(['template'])->findOrFail($id);

        // operator hanya boleh lihat dokumen fakultasnya
        $docFakultasId = $doc->template->fakultas_id ?? null;
        abort_unless($docFakultasId && (int) $docFakultasId === (int) $op->fakultas_id, 403);

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
        abort_unless($docFakultasId && (int) $docFakultasId === (int) $op->fakultas_id, 403);

        abort_unless($doc->docx_path, 404, 'DOCX belum tersedia');
        abort_unless(Storage::disk('local')->exists($doc->docx_path), 404, 'File DOCX tidak ditemukan');

        $filename = 'dokumen-' . $doc->id . '.docx';
        return Storage::disk('local')->download($doc->docx_path, $filename);
    }

    public function pengajuanHasil()
    {
        $op = \App\Models\operator::where('user_id', auth()->id())->first();

        $pengajuans = StudentDocument::with(['user.mahasiswa.fakultas', 'template'])
            ->when($op, function ($q) use ($op) {
                $q->whereHas('template', function ($t) use ($op) {
                    $t->where('fakultas_id', $op->fakultas_id);
                });
            })
            ->whereIn('status', ['completed', 'rejected'])
            ->orderByDesc('updated_at')
            ->get();

        return view('template::pengajuan-hasil', compact('pengajuans'));
    }
}
