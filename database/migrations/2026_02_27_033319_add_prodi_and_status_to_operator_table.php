<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operator', function (Blueprint $table) {

            if (!Schema::hasColumn('operator', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable()->after('fakultas_id');
            }

            if (!Schema::hasColumn('operator', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('prodi_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('operator', function (Blueprint $table) {
            $table->dropColumn(['prodi_id', 'is_active']);
        });
    }
};
