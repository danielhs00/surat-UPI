<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {

            if (!Schema::hasColumn('student_documents', 'catatan_wadek')) {
                $table->text('catatan_wadek')->nullable();
            }

            if (!Schema::hasColumn('student_documents', 'signed_by')) {
                $table->unsignedBigInteger('signed_by')->nullable();
            }

            if (!Schema::hasColumn('student_documents', 'signed_at')) {
                $table->timestamp('signed_at')->nullable();
            }

            if (!Schema::hasColumn('student_documents', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_wadek',
                'signed_by',
                'signed_at',
                'nomor_surat'
            ]);
        });
    }
};
