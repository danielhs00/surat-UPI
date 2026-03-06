<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable()->after('status');
            $table->text('catatan_wadek')->nullable()->after('nomor_surat');

            $table->unsignedBigInteger('signed_by')->nullable()->after('catatan_wadek'); // user_id wadek
            $table->timestamp('signed_at')->nullable()->after('signed_by');

            $table->string('signed_pdf_path')->nullable()->after('pdf_path'); // hasil pdf yang sudah ditandatangan
        });
    }

    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_surat','catatan_wadek','signed_by','signed_at','signed_pdf_path'
            ]);
        });
    }
};