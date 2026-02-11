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
        Schema::rename('arsip_sertifikat', 'arsip_sertifikats');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('arsip_sertifikats', 'arsip_sertifikat');
    }
};
