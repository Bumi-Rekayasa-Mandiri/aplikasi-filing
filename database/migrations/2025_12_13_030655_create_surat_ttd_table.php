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
        Schema::create('surat_ttd', function (Blueprint $table) {
    $table->id();
    $table->foreignId('surat_id')->constrained('surat')->cascadeOnDelete();
    $table->foreignId('file_upload_id')->constrained('file_uploads');
    $table->string('nama_penandatangan');
    $table->string('jabatan_penandatangan');
    $table->unsignedInteger('urutan');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_ttd');
    }
};
