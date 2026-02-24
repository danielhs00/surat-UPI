<?php

namespace Modules\Wadek\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Mahasiswa\Models\StudentDocument;
use Modules\Wadek\Models\Wadek;

class WadekController extends Controller
{
    private function currentWadek(): Wadek
    {
        return Wadek::where('user_id', auth()->id())->firstOrFail();
    }

    private function authorizeDocumentForWadek(StudentDocument $doc, Wadek $wdk): void
    {
        // dokumen harus berasal dari fakultas wadek (lewat template)
        $templateFakultasId = optional($doc->template)->fakultas_id;

        if (!$templateFakultasId || $templateFakultasId != $wdk->fakultas_id) {
            abort(403, 'Dokumen bukan untuk fakultas Anda.');
        }
    }

    public function show($id)
    {
        $wdk = $this->currentWadek();

        $document = StudentDocument::with(['user.mahasiswa.fakultas', 'template'])
            ->findOrFail($id);

        $this->authorizeDocumentForWadek($document, $wdk);

        return view('wadek::show', compact('document', 'wdk'));
    }

    public function uploadSignature(Request $request)
    {
        $wdk = $this->currentWadek();

        $data = $request->validate([
            'ttd' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $path = $data['ttd']->store('wadek/ttd', 'local'); // storage/app/wadek/ttd/...

        $wdk->update([
            'ttd_path' => $path,
            'ttd_uploaded_at' => now(),
        ]);

        return back()->with('success', 'TTD berhasil diupload.');
    }

    public function viewPdf($id)
    {
        $wdk = $this->currentWadek();

        $document = StudentDocument::with(['template'])
            ->findOrFail($id);

        $this->authorizeDocumentForWadek($document, $wdk);

        // pilih pdf signed kalau ada, kalau belum pakai pdf_path biasa
        $relPath = $document->signed_pdf_path ?: $document->pdf_path;

        if (!$relPath || !Storage::disk('local')->exists($relPath)) {
            abort(404, 'PDF tidak ditemukan.');
        }

        $abs = Storage::disk('local')->path($relPath);
        return response()->file($abs, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function sign(Request $request, $id)
    {
        $wdk = $this->currentWadek();

        $document = StudentDocument::with(['template', 'user.mahasiswa'])
            ->findOrFail($id);

        $this->authorizeDocumentForWadek($document, $wdk);

        // Pastikan status memang sudah dikirim operator ke wadek
        if ($document->status !== 'sent_to_wadek') {
            return back()->with('error', 'Status dokumen belum "sent_to_wadek".');
        }

        $data = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:100'],
            'catatan_wadek' => ['nullable', 'string', 'max:2000'],
        ]);

        // --- update data utama ---
        $document->nomor_surat = $data['nomor_surat'];
        $document->catatan_wadek = $data['catatan_wadek'] ?? null;
        $document->signed_by = auth()->id();
        $document->signed_at = now();

        // --- Jika mau tempel TTD ke PDF (FPDI) ---
        // Kalau belum upload TTD atau belum install fpdi, kita tetap approve tanpa tempel ttd
        $signedRel = null;

        if ($wdk->ttd_path && Storage::disk('local')->exists($wdk->ttd_path) && $document->pdf_path && Storage::disk('local')->exists($document->pdf_path)) {
            try {
                // butuh: composer require setasign/fpdi-fpdf
                $srcPdf = Storage::disk('local')->path($document->pdf_path);
                $sigImg = Storage::disk('local')->path($wdk->ttd_path);

                $signedRel = "wadek/signed/{$document->id}.pdf";
                $signedAbs = Storage::disk('local')->path($signedRel);

                if (!is_dir(dirname($signedAbs))) {
                    mkdir(dirname($signedAbs), 0775, true);
                }

                $pdf = new \setasign\Fpdi\Fpdi();
                $pageCount = $pdf->setSourceFile($srcPdf);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($tplId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);

                    // tempel hanya di halaman terakhir
                    if ($pageNo === $pageCount) {
                        // posisi kira-kira kanan bawah (sesuaikan nanti)
                        $x = $size['width'] - 70;   // 70mm dari kanan
                        $y = $size['height'] - 60;  // 60mm dari bawah
                        $w = 55;                    // lebar ttd

                        $pdf->Image($sigImg, $x, $y, $w);

                        // teks nomor surat
                        $pdf->SetFont('Arial', '', 10);
                        $pdf->SetXY(15, $size['height'] - 25);
                        $pdf->Cell(0, 6, 'Nomor Surat: ' . $data['nomor_surat']);
                    }
                }

                $pdf->Output($signedAbs, 'F');
                $document->signed_pdf_path = $signedRel;
                $document->pdf_path = $signedRel;
            } catch (\Throwable $e) {
                // gagal tempel, tetap lanjut approve (biar user ga stuck)
                // simpan error ke convert_error kalau mau
            }
        }

        $document->signed_by = auth()->id();
        $document->signed_at = now();
        $document->status = 'signed';
        $document->save();

        return redirect()
            ->route('wadek.documents.show', $document->id)
            ->with('success', 'Berhasil input nomor surat & tanda tangan.');
    }

    public function reject(Request $request, $id)
    {
        $wdk = $this->currentWadek();

        $document = StudentDocument::with(['template'])
            ->findOrFail($id);

        $this->authorizeDocumentForWadek($document, $wdk);

        $data = $request->validate([
            'catatan_wadek' => ['required', 'string', 'max:2000'],
        ]);

        $document->status = 'rejected';
        $document->catatan_wadek = $data['catatan_wadek'];
        $document->signed_by = auth()->id();
        $document->signed_at = now();
        $document->save();

        return redirect()
            ->route('wadek.documents.show', $document->id)
            ->with('success', 'Dokumen ditolak dan dikembalikan ke operator.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'catatan_wadek' => 'nullable|string',
        ]);

        $doc = \Modules\Mahasiswa\Models\StudentDocument::findOrFail($id);

        // TODO: generate signed pdf -> simpan ke signed_pdf_path
        // $signedPath = ...

        $doc->update([
            'nomor_surat' => $request->nomor_surat,
            'catatan_wadek' => $request->catatan_wadek,
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'signed_pdf_path' => $signedPath ?? $doc->signed_pdf_path, // kalau sudah kamu isi
            'status' => 'signed',
        ]);

        return back()->with('success', 'Dokumen berhasil ditandatangani.');
    }

    public function downloadDocx($id)
    {
        $wdk = $this->currentWadek();

        $document = StudentDocument::with(['template'])->findOrFail($id);
        $this->authorizeDocumentForWadek($document, $wdk);

        if (!$document->docx_path || !Storage::disk('local')->exists($document->docx_path)) {
            abort(404, 'DOCX tidak ditemukan.');
        }

        return Storage::disk('local')->download($document->docx_path, 'dokumen-' . $document->id . '.docx');
    }
}
