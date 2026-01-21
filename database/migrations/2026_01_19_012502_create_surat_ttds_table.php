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
        Schema::create('surat_ttds', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('surat_id')
            ->constrained('surat')
            ->cascadeOnDelete();

            $table->string('nama_penandatangan');
            $table->string('jabatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_ttds');
    }
};
