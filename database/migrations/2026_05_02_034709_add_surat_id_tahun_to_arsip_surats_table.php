<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip_surats', function (Blueprint $table) {
            // FK ke surat asli — untuk restore & traceability
            $table->foreignId('surat_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('surat')
                  ->nullOnDelete(); // jika surat di-hard-delete, arsip tetap ada

            // Tahun arsip — untuk filter per tahun di index arsip
            $table->smallInteger('tahun')->nullable()->after('surat_id')->index();

            // Timestamp kapan diarsipkan (berbeda dari created_at record arsip)
            $table->timestamp('archived_at')->nullable()->after('tahun');
        });
    }

    public function down(): void
    {
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->dropForeign(['surat_id']);
            $table->dropColumn(['surat_id', 'tahun', 'archived_at']);
        });
    }
};