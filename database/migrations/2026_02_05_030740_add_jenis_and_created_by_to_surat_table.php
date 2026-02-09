<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->string('jenis', 50)
                  ->after('id')
                  ->nullable()
                  ->index();

            $table->foreignId('created_by')
                  ->after('status')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['jenis', 'created_by']);
        });
    }
};