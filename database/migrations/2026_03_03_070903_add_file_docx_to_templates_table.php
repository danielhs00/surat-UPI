<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Hapus google_docs_url dan tambahkan file_docx_path
            $table->dropColumn('google_docs_url');
            $table->string('file_docx_path')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('file_docx_path');
            $table->string('google_docs_url')->nullable();
        });
    }
};