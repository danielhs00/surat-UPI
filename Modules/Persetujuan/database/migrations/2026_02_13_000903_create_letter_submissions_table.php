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
        Schema::create('letter_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('letter_templates');
            $table->foreignId('student_id')->constrained('users');
            $table->string('filled_file_path');
            $table->string('signed_file_path')->nullable();
            $table->string('status')->default('submitted');
            $table->text('operator_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_submissions');
    }
};
