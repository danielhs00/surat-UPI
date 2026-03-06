<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();

            $table->string('title')->nullable();

            $table->string('docx_path')->nullable(); // file upload mahasiswa
            $table->string('pdf_path')->nullable();  // hasil convert

            $table->string('status')->default('draft');
            // draft|uploaded|converting|converted|failed

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            $table->text('convert_error')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'updated_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};