<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek kolom satu per satu supaya aman
        if (Schema::hasColumn('student_documents', 'nomor_surat')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->dropColumn('nomor_surat');
            });
        }

        if (Schema::hasColumn('student_documents', 'catatan_wadek')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->dropColumn('catatan_wadek');
            });
        }

        if (Schema::hasColumn('student_documents', 'signed_by')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->dropColumn('signed_by');
            });
        }

        if (Schema::hasColumn('student_documents', 'signed_at')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->dropColumn('signed_at');
            });
        }

        if (Schema::hasColumn('student_documents', 'signed_pdf_path')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->dropColumn('signed_pdf_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable()->after('status');
            $table->text('catatan_wadek')->nullable()->after('nomor_surat');
            $table->unsignedBigInteger('signed_by')->nullable()->after('catatan_wadek');
            $table->timestamp('signed_at')->nullable()->after('signed_by');
            $table->string('signed_pdf_path')->nullable()->after('pdf_path');
        });
    }
};