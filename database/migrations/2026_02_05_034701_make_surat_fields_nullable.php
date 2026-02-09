<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->string('perihal')->nullable()->change();
            
            $table->text('isi_surat')->nullable()->change();
            
            $table->string('pdf_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
      
            $table->string('perihal')->nullable(false)->change();
            
            $table->text('isi_surat')->nullable(false)->change();
            
            $table->string('pdf_path')->nullable(false)->change();
        });
    }
};