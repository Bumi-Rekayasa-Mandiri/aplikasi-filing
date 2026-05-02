<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            // Soft-hide: surat tetap di tabel, tapi tidak muncul di manajemen
            $table->timestamp('archived_at')->nullable()->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};