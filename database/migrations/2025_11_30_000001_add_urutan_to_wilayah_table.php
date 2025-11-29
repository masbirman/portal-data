<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->integer('urutan')->default(999)->after('logo');
        });

        // Set urutan sesuai kebutuhan
        $urutan = [
            'Kota Palu' => 1,
            'Donggala' => 2,
            'Poso' => 3,
            'Morowali' => 4,
            'Banggai' => 5,
            'Banggai Kepulauan' => 6,
            'Tolitoli' => 7,
            'Buol' => 8,
            'Parigi Moutong' => 9,
            'Tojo Una-Una' => 10,
            'Sigi' => 11,
            'Banggai Laut' => 12,
            'Morowali Utara' => 13,
        ];

        foreach ($urutan as $nama => $order) {
            DB::table('wilayah')->where('nama', $nama)->update(['urutan' => $order]);
        }
    }

    public function down(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
