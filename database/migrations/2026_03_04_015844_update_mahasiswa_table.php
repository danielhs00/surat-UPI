<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {

            $table->dropColumn('angkatan');
            $table->dropColumn('prodi');

            $table->foreignId('prodi_id')
                  ->after('fakultas_id')
                  ->constrained('prodi')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {

            $table->string('prodi');
            $table->integer('angkatan');

            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }
};