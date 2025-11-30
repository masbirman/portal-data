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
            $table->string('ip_address', 45)->nullable()->after('email');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_requests', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropColumn('ip_address');
        });
    }
};
