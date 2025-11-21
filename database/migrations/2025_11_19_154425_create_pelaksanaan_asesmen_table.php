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
        Schema::create('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siklus_asesmen_id')->constrained('siklus_asesmen');
            $table->foreignId('sekolah_id')->constrained('sekolah');
            $table->foreignId('wilayah_id')->constrained('wilayah');
            $table->enum('status_pelaksanaan', ['Mandiri', 'Menumpang']);
            $table->enum('moda_pelaksanaan', ['Online', 'Semi Online']);
            $table->decimal('partisipasi_literasi', 5, 2);
            $table->decimal('partisipasi_numerasi', 5, 2);
            $table->string('tempat_pelaksanaan');
            $table->string('nama_penanggung_jawab');
            $table->string('nama_proktor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaan_asesmen');
    }
};
