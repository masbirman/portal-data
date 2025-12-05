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
        Schema::table('sekolah', function (Blueprint $table) {
            // Add latitude and longitude columns for school locations
            $table->decimal('latitude', 10, 8)->nullable()->after('status_sekolah');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('alamat')->nullable()->after('longitude');
            
            // Add index for spatial queries
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude', 'alamat']);
        });
    }
};
