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
     Schema::create('nomor_surat_logs', function (Blueprint $table) {
    $table->id();

    $table->foreignId('surat_id')
          ->constrained('surat')
          ->cascadeOnDelete();

    $table->integer('running_number');
    $table->string('nomor_surat');

    $table->integer('tahun');
    $table->integer('bulan');

    $table->string('kode_jenis')->nullable();

    $table->timestamps();

    $table->unique(['running_number', 'tahun', 'kode_jenis']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_log');
    }
};
