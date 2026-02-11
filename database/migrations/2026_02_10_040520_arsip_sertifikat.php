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
        Schema::create('arsip_sertifikat',
        function (Blueprint $table){
            $table->id();
            $table->string('nama_sertifikat');
            $table->string('nomor_sertifikat')->nullable();
            $table->string('jenis_sertifikat')->nullable();
            $table->string('instansi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
