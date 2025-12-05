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
        Schema::table('wilayah', function (Blueprint $table) {
            // Add latitude and longitude columns for point coordinates
            $table->decimal('latitude', 10, 8)->nullable()->after('urutan');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Add geometry column for polygon boundaries (optional, for future use)
            // Using JSON to store polygon coordinates as GeoJSON format
            $table->json('geometry')->nullable()->after('longitude');
            
            // Add index for spatial queries
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude', 'geometry']);
        });
    }
};
