<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('google_docs_url')->nullable()->after('deskripsi');
            $table->dropColumn(['file_docx_path', 'file_pdf_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('google_docs_url');
            $table->string('file_docx_path')->nullable();
            $table->string('file_pdf_path')->nullable();
        });
    }
};
