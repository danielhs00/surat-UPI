<?php

namespace Modules\Mahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Mahasiswa\Jobs\ConvertDocxToPdf;
use Modules\Mahasiswa\Models\Template;
use Modules\Mahasiswa\Models\StudentDocument;
use Modules\Mahasiswa\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Filesystem\Filesystem;

class StudentDocumentController extends Controller
{
    use AuthorizesRequests;
    public function downloadTemplate(Template $template)
    {
        // pastikan template aktif (opsional)
        abort_unless($template->is_active, 404);

        $path = $template->file_docx_path; // sesuai yang disimpan operator

        abort_unless($path, 404, 'File template tidak ditemukan');
        abort_unless(Storage::disk('public')->exists($path), 404, 'File template tidak ditemukan di storage');

        $filename = str_replace(' ', '_', ($template->nama_template ?? 'template')) . '.docx';
        return Storage::disk('public')->download($path, $filename);
    }

    public function uploadDocx(Request $request, StudentDocument $document)
    {
        $this->authorize('update', $document);

        $data = $request->validate([
            'docx' => ['required', 'file', 'mimes:doc,docx', 'max:10240'], // 10MB
        ]);

        $file = $data['docx'];

        // simpan docx ke storage
        $docxRelPath = $file->storeAs(
            'mahasiswa/documents/' . Auth::id() . '/docx',
            $document->id . '-' . time() . '.' . $file->getClientOriginalExtension(),
            'local'
        );

        // versi baru
        $nextVersion = (int) ($document->versions()->max('version') ?? 0) + 1;

        DocumentVersion::create([
            'student_document_id' => $document->id,
            'version' => $nextVersion,
            'docx_path' => $docxRelPath,
            'note' => 'Upload v' . $nextVersion,
        ]);

        $document->update([
            'docx_path' => $docxRelPath,
            'pdf_path' => null,
            'status' => 'uploaded',
            'submitted_at' => now(),
            'convert_error' => null,
        ]);

        // dispatch queue convert
        ConvertDocxToPdf::dispatch($document->id);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'DOCX berhasil diupload. PDF sedang diproses (queue).');
    }

    public function downloadPdf(StudentDocument $document)
    {
        $path = $document->signed_pdf_path ?: $document->pdf_path;

        abort_unless($path, 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function fromTemplate(Template $template)
    {
        abort_unless($template->is_active, 404);

        $doc = StudentDocument::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'title' => $template->nama_template,
            'status' => 'draft',
        ]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Draft berhasil dibuat. Silakan upload DOCX untuk diproses.');
    }

    public function createFromTemplate(Template $template)
    {
        abort_unless($template->is_active, 404);

        $doc = StudentDocument::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'title' => $template->nama_template,
            'status' => 'draft',
        ]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Draft berhasil dibuat. Silakan upload DOCX untuk diproses.');
    }

    public function viewPdf($id)
    {
        $document = \Modules\Mahasiswa\Models\StudentDocument::findOrFail($id);

        abort_unless($document->user_id === auth()->id(), 403);

        $path = $document->signed_pdf_path ?: $document->pdf_path;
        abort_unless($path, 404, 'PDF belum tersedia');
        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($path), 404, 'File PDF tidak ditemukan');

        return response()->file(
            \Illuminate\Support\Facades\Storage::disk('local')->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dokumen-' . $document->id . '.pdf"',
            ]
        );
    }

    //RESET STATUS DOKUMEN
    public function dashboard(Request $request)
    {
        $status = $request->query('status', 'all');

        dd([
            'full_url' => $request->fullUrl(),
            'status_query' => $request->query('status'),
            'status_var' => $status,
        ]);

        // kode bawah ini tidak akan jalan karena dd() menghentikan program
    }

    public function resubmit($id)
    {
        $document = \Modules\Mahasiswa\Models\StudentDocument::findOrFail($id);

        // pastikan dokumen milik mahasiswa yang login
        abort_unless($document->user_id === auth()->id(), 403);

        // hanya boleh ajukan ulang jika ditolak
        if ($document->status !== 'rejected') {
            return back()->with('error', 'Dokumen tidak dapat diajukan ulang.');
        }

        $document->status = 'revisi';
        $document->catatan_operator = null;
        $document->hidden_in_dashboard = 0;

        $document->approved_at = null;
        $document->approved_by = null;
        $document->nomor_surat = null;
        $document->signed_pdf_path = null;

        $document->save();

        return redirect()
            ->route('mahasiswa.dashboard')
            ->with('success', 'Dokumen berhasil diajukan ulang. Silakan upload ulang berkas.');
    }
}
