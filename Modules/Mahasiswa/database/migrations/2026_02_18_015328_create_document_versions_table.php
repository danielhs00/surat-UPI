<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_document_id')
                ->constrained('student_documents')
                ->cascadeOnDelete();

            $table->unsignedInteger('version');
            $table->string('docx_path');
            $table->string('pdf_path')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['student_document_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};