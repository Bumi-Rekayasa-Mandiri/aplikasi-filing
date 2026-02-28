<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_ttds', function (Blueprint $table) {
            $table->integer('urutan')->default(1)->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_ttds', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};