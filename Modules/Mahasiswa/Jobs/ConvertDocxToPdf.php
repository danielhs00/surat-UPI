<?php

namespace Modules\Mahasiswa\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Modules\Mahasiswa\Models\StudentDocument;

class ConvertDocxToPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120; // detik
    public int $tries = 3;

    public function __construct(public int $documentId) {}

    public function handle(): void
    {
        $doc = StudentDocument::findOrFail($this->documentId);

        if (!$doc->docx_path) {
            $doc->update([
                'status' => 'failed',
                'convert_error' => 'DOCX file not found (docx_path is null).',
            ]);
            return;
        }

        $disk = Storage::disk('local');

        // Pastikan file benar-benar ada di disk local (root kamu = storage/app/private)
        if (!$disk->exists($doc->docx_path)) {
            $doc->update([
                'status' => 'failed',
                'convert_error' => 'DOCX not found on disk: ' . $disk->path($doc->docx_path),
            ]);
            return;
        }

        $doc->update([
            'status' => 'converting',
            'convert_error' => null,
        ]);

        $absoluteDocx = $disk->path($doc->docx_path);

        // output folder sementara (di disk local)
        $outDirRel = 'mahasiswa/pdf_tmp/' . $doc->id;
        $outDirAbs = $disk->path($outDirRel);

        if (!is_dir($outDirAbs)) {
            @mkdir($outDirAbs, 0775, true);
        }

        // Path LibreOffice (Windows)
        $soffice = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';

        $process = new Process([
            $soffice,
            '--headless',
            '--nologo',
            '--nofirststartwizard',
            '--convert-to', 'pdf',
            '--outdir', $outDirAbs,
            $absoluteDocx,
        ]);

        $process->setTimeout(110);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput() ?: $process->getOutput());

            $doc->update([
                'status' => 'failed',
                'convert_error' => $error ?: 'Unknown conversion error.',
            ]);

            Log::error('DOCX->PDF convert failed', [
                'document_id' => $doc->id,
                'docx' => $absoluteDocx,
                'outdir' => $outDirAbs,
                'error' => $error,
            ]);

            $this->cleanupTmpDir($outDirAbs);
            return;
        }

        // Jangan tebak nama file PDF, ambil pdf yang dihasilkan dari outdir
        $pdfFiles = glob($outDirAbs . DIRECTORY_SEPARATOR . '*.pdf') ?: [];
        if (!$pdfFiles) {
            $pdfFiles = glob($outDirAbs . DIRECTORY_SEPARATOR . '*.PDF') ?: [];
        }

        if (!$pdfFiles) {
            $doc->update([
                'status' => 'failed',
                'convert_error' => 'PDF not generated in outdir: ' . $outDirAbs,
            ]);

            $this->cleanupTmpDir($outDirAbs);
            return;
        }

        $generatedPdfAbs = $pdfFiles[0];

        // simpan pdf final ke disk local
        $finalPdfRel = 'mahasiswa/documents/' . $doc->user_id . '/' . $doc->id . '.pdf';
        $disk->put($finalPdfRel, file_get_contents($generatedPdfAbs));

        $doc->update([
            'pdf_path' => $finalPdfRel,
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        // update versi terakhir kalau ada
        $latestVersion = $doc->versions()->latest('version')->first();
        if ($latestVersion) {
            $latestVersion->update(['pdf_path' => $finalPdfRel]);
        }

        $this->cleanupTmpDir($outDirAbs);
    }

    private function cleanupTmpDir(string $outDirAbs): void
    {
        // Hapus semua file di folder tmp
        foreach (glob($outDirAbs . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($outDirAbs);
    }
}