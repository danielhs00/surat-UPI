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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();

            // relasi fakultas (biar template per fakultas)
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();

            // siapa operator yang upload (user_id role operator)
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            $table->string('nama_template'); // contoh: "Surat Aktif Kuliah"
            $table->string('jenis_surat')->nullable(); // opsional grouping
            $table->text('deskripsi')->nullable();

            // file word & hasil pdf (opsional)
            $table->string('file_docx_path'); // storage path
            $table->string('file_pdf_path')->nullable(); // nanti untuk preview

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
