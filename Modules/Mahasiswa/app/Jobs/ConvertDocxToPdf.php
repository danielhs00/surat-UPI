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
use Modules\Mahasiswa\Models\DocumentVersion;

class ConvertDocxToPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120; // detik
    public int $tries = 3;

    public function __construct(public int $documentId) {}

    /**
     * Execute the job.
     */
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

        $doc->update([
            'status' => 'converting',
            'convert_error' => null,
        ]);

        $disk = Storage::disk('local');

        $absoluteDocx = storage_path('app/' . $doc->docx_path);

        // output folder sementara
        $outDirRel = 'mahasiswa/pdf_tmp/' . $doc->id;
        $outDirAbs = storage_path('app/' . $outDirRel);
        if (!is_dir($outDirAbs)) {
            mkdir($outDirAbs, 0775, true);
        }

        // Command LibreOffice
        // Linux/Mac: soffice
        // Windows: mungkin perlu full path soffice.exe
        $soffice = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';

        $process = new Process([
            $soffice,
            '--headless',
            '--nologo',
            '--nofirststartwizard',
            '--convert-to',
            'pdf',
            '--outdir',
            $outDirAbs,
            $absoluteDocx,
        ]);

        $process->setTimeout(110);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput() ?: $process->getOutput();

            $doc->update([
                'status' => 'failed',
                'convert_error' => $error,
            ]);

            Log::error('DOCX->PDF convert failed', [
                'document_id' => $doc->id,
                'error' => $error,
            ]);

            return;
        }

        // hasil pdf biasanya bernama sama dengan docx tapi ekstensi pdf
        $baseName = pathinfo($absoluteDocx, PATHINFO_FILENAME);
        $generatedPdfAbs = $outDirAbs . DIRECTORY_SEPARATOR . $baseName . '.pdf';

        if (!file_exists($generatedPdfAbs)) {
            $doc->update([
                'status' => 'failed',
                'convert_error' => 'PDF not generated. Expected: ' . $generatedPdfAbs,
            ]);
            return;
        }

        // pindahkan pdf ke lokasi final
        $finalPdfRel = 'mahasiswa/documents/' . $doc->user_id . '/' . $doc->id . '.pdf';
        $disk->put($finalPdfRel, file_get_contents($generatedPdfAbs));

        $doc->update([
            'pdf_path' => $finalPdfRel,
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        // update versi terakhir (kalau pakai versioning)
        $latestVersion = $doc->versions()->latest('version')->first();
        if ($latestVersion) {
            $latestVersion->update(['pdf_path' => $finalPdfRel]);
        }

        // bersihkan tmp
        @unlink($generatedPdfAbs);
        @rmdir($outDirAbs);
    }
}
