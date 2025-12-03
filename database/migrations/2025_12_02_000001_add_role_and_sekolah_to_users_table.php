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
        Schema::table('users', function (Blueprint $table) {
            // Skip columns that already exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin_wilayah', 'user_sekolah'])
                    ->default('user_sekolah')
                    ->after('password');
            }
            if (!Schema::hasColumn('users', 'sekolah_id')) {
                $table->foreignId('sekolah_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('sekolah')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('sekolah_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropColumn(['role', 'sekolah_id', 'is_active']);
        });
    }
};
