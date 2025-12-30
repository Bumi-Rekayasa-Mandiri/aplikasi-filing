<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('surat_ttd', function (Blueprint $table) {
            $table->string('nama_penandatangan')->nullable()->change();
            $table->string('jabatan_penandatangan')->nullable()->change();
            $table->unsignedInteger('urutan')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('surat_ttd', function (Blueprint $table) {
            $table->string('nama_penandatangan')->nullable(false)->change();
            $table->string('jabatan_penandatangan')->nullable(false)->change();
            $table->unsignedInteger('urutan')->nullable(false)->change();
        });
    }
};