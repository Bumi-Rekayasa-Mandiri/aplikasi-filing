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
        Schema::table('surat', function (Blueprint $table) {
        $table->string('nama')->nullable()->after('isi_surat');
        $table->string('jabatan_terakhir')->nullable()->after('nama');
        $table->string('departemen')->nullable()->after('jabatan_terakhir');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('surat', function (Blueprint $table) {
        $table->dropColumn([
            'nama',
            'jabatan_terakhir',
            'departemen',
        ]);
    });
    }
};
