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
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->string('tujuan')->nullable();
            $table->string('jenis_surat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip_surats', function (Blueprint $table) {
            //
        });
    }
};
