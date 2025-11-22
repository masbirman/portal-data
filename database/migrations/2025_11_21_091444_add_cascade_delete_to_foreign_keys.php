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
        // Drop existing foreign key constraint
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
        });

        // Add foreign key with cascade delete
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->foreign('sekolah_id')
                ->references('id')
                ->on('sekolah')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop cascade foreign key
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
        });

        // Restore original foreign key without cascade
        Schema::table('pelaksanaan_asesmen', function (Blueprint $table) {
            $table->foreign('sekolah_id')
                ->references('id')
                ->on('sekolah');
        });
    }
};
