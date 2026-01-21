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
    Schema::table('nomor_surat_logs', function (Blueprint $table) {

        // 1. Hapus kolom tidak resmi
        if (Schema::hasColumn('nomor_surat_logs', 'periode')) {
            $table->dropColumn('periode');
        }

        // 2. Pastikan bulan integer kecil
        $table->unsignedTinyInteger('bulan')->change();

        // 3. Perbaiki unique index
        $table->dropUnique('nomor_surat_logs_tahun_running_unique');

        $table->unique(
            ['tahun', 'bulan', 'kode_jenis', 'running_number'],
            'nomor_surat_unique'
        );
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_logs');
    }
};