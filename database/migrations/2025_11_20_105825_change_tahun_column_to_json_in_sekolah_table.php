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
        // PENTING: Perintah 'change()' memerlukan paket 'doctrine/dbal'. 
        // Pastikan Anda sudah menginstalnya jika belum (composer require doctrine/dbal)
        
        Schema::table('sekolah', function (Blueprint $table) {
            // Mengubah tipe kolom 'tahun' dari TEXT menjadi JSON
            $table->json('tahun')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            // Mengubah kembali tipe kolom 'tahun' dari JSON menjadi TEXT jika diperlukan rollback
            // Asumsi: tipe sebelumnya adalah TEXT
            $table->text('tahun')->change();
        });
    }
};