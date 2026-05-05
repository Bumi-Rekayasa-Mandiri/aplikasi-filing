<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            // Timestamp kapan PDF/DOCX terakhir di-regenerate
            // Digunakan untuk:
            //   - Tampilkan ke user di Show & Edit page sebagai transparency
            //   - Indikator stale-nya file vs data DB
            $table->timestamp('last_regenerated_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropColumn('last_regenerated_at');
        });
    }
};