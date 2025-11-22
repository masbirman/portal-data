<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Wilayah;
use App\Models\Sekolah;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Support\Facades\DB;

$correctId = 10; // Kab. Tojo Unauna
$wrongId = 17;   // Kab. Tojo Una-una

$correct = Wilayah::find($correctId);
$wrong = Wilayah::find($wrongId);

if (!$correct || !$wrong) {
    echo "Salah satu ID tidak ditemukan.\n";
    exit;
}

echo "Migrating data from '{$wrong->nama}' (ID: $wrongId) to '{$correct->nama}' (ID: $correctId)...\n";

DB::transaction(function () use ($correctId, $wrongId) {
    // Update Sekolah
    $sekolahCount = Sekolah::where('wilayah_id', $wrongId)->update(['wilayah_id' => $correctId]);
    echo "Updated $sekolahCount Sekolah records.\n";

    // Update PelaksanaanAsesmen
    $asesmenCount = PelaksanaanAsesmen::where('wilayah_id', $wrongId)->update(['wilayah_id' => $correctId]);
    echo "Updated $asesmenCount PelaksanaanAsesmen records.\n";

    // Delete wrong Wilayah
    Wilayah::destroy($wrongId);
    echo "Deleted wrong Wilayah (ID: $wrongId).\n";
});

echo "Migration completed successfully.\n";
