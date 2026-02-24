<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wadek', function (Blueprint $table) {
            $table->string('ttd_path')->nullable()->after('fakultas_id');
            $table->timestamp('ttd_uploaded_at')->nullable()->after('ttd_path');
        });
    }

    public function down(): void
    {
        Schema::table('wadek', function (Blueprint $table) {
            $table->dropColumn(['ttd_path', 'ttd_uploaded_at']);
        });
    }
};