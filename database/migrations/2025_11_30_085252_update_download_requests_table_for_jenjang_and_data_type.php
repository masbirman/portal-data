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
        Schema::table('download_requests', function (Blueprint $table) {
            // Drop old jenjang column
            $table->dropColumn('jenjang');
            
            // Add jenjang_pendidikan_id foreign key
            $table->foreignId('jenjang_pendidikan_id')->after('wilayah_id')->constrained('jenjang_pendidikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_requests', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['jenjang_pendidikan_id']);
            $table->dropColumn('jenjang_pendidikan_id');
            
            // Restore old jenjang column
            $table->string('jenjang')->nullable()->after('wilayah_id');
        });
    }
};
