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

class StudentDocumentController extends Controller
{
    use AuthorizesRequests;
    public function downloadTemplate(Template $template)
    {
        // mahasiswa boleh download template aktif
        abort_unless($template->is_active, 404);

        $path = $template->docx_path;
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $template->name . '.docx');
    }

    public function createFromTemplate(Template $template)
    {
        abort_unless($template->is_active, 404);

        $doc = StudentDocument::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'title' => $template->name . ' - ' . now()->format('Y-m-d H:i'),
            'status' => 'draft',
        ]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Dokumen draft dibuat. Silakan download template dan upload hasil edit DOCX.');
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
        $this->authorize('view', $document);

        abort_unless($document->pdf_path, 404);
        abort_unless(Storage::disk('local')->exists($document->pdf_path), 404);

        $filename = ($document->title ?: 'document') . '.pdf';
        return Storage::disk('local')->download($document->pdf_path, $filename);
    }
}
