<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('wilayah_id')->nullable()->change();
            $table->unsignedBigInteger('jenjang_pendidikan_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('download_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('wilayah_id')->nullable(false)->change();
            $table->unsignedBigInteger('jenjang_pendidikan_id')->nullable(false)->change();
        });
    }
};
