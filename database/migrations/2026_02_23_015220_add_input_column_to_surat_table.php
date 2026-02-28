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
        $table->string('project')->nullable()->after('isi_surat');
        $table->string('material')->nullable()->after('project');
        $table->string('alamat')->nullable()->after('material');
        $table->string('masa_garansi')->nullable()->after('alamat');
        $table->string('no_ktp')->nullable()->after('masa_garansi');
        $table->string('nominal')->nullable()->after('no_ktp');
        $table->string('nominal_bagihasil')->nullable()->after('nominal');
        $table->string('hasil_denda')->nullable()->after('nominal_bagihasil');
        $table->string('keringanan_denda')->nullable()->after('hasil_denda');
        $table->string('lampiran')->nullable()->after('keringanan_denda');
        $table->string('item_pembelian')->nullable()->after('lampiran');
        $table->string('merk')->nullable()->after('item_pembelian');
        $table->string('warna')->nullable()->after('merk');
        $table->string('rangka')->nullable()->after('warna');
        $table->string('gambar_materai')->nullable()->after('rangka');
        $table->string('lokasi_kerja')->nullable()->after('gambar_materai');
        $table->string('jenis_pekerjaan')->nullable()->after('lokasi_kerja');
        $table->string('waktu')->nullable()->after('jenis_pekerjaan');
        $table->string('jam_kerja')->nullable()->after('waktu');
        $table->string('jumlah_pekerja')->nullable()->after('jam_kerja');
        $table->string('apd')->nullable()->after('jumlah_pekerja');
        $table->string('periode')->nullable()->after('apd');
        $table->string('no_pekerja')->nullable()->after('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            //
        });
    }
};
