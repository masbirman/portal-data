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
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->index('siklus_asesmen_id', 'idx_siklus_asesmen');
            $table->index('wilayah_id', 'idx_pa_wilayah');
            $table->index(['siklus_asesmen_id', 'wilayah_id'], 'idx_siklus_wilayah');
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->index('jenjang_pendidikan_id', 'idx_jenjang');
            $table->index('wilayah_id', 'idx_sekolah_wilayah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->dropIndex('idx_siklus_asesmen');
            $table->dropIndex('idx_pa_wilayah');
            $table->dropIndex('idx_siklus_wilayah');
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropIndex('idx_jenjang');
            $table->dropIndex('idx_sekolah_wilayah');
        });
    }
};
