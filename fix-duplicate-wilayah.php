<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Wilayah;
use App\Models\Sekolah;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Support\Facades\DB;

echo "=== SCRIPT PEMBERSIHAN WILAYAH DUPLIKAT ===\n\n";

// Mapping wilayah duplikat: ID lama => ID baru (yang akan dipertahankan)
$duplicateMapping = [
    14 => 2,  // Kab. Donggala => Kabupaten Donggala
    15 => 6,  // Kab. Poso => Kabupaten Poso
    16 => 7,  // Kab. Morowali => Kabupaten Morowali
    17 => 9,  // Kab. Banggai => Kabupaten Banggai
    18 => 10, // Kab. Banggai Kepulauan => Kabupaten Banggai Kepulauan
    19 => 12, // Kab. Tolitoli => Kabupaten Toli-Toli
    20 => 13, // Kab. Buol => Kabupaten Buol
    21 => 4,  // Kab. Parigi Moutong => Kabupaten Parigi Moutong
    22 => 5,  // Kab. Tojo Unauna => Kabupaten Tojo Una-Una
    23 => 3,  // Kab. Sigi => Kabupaten Sigi
    24 => 11, // Kab. Banggai Laut => Kabupaten Banggai Laut
    25 => 8,  // Kab. Morowali Utara => Kabupaten Morowali Utara
];

DB::beginTransaction();

try {
    foreach ($duplicateMapping as $oldId => $newId) {
        $oldWilayah = Wilayah::find($oldId);
        $newWilayah = Wilayah::find($newId);
        
        if (!$oldWilayah || !$newWilayah) {
            echo "SKIP: Wilayah ID $oldId atau $newId tidak ditemukan\n";
            continue;
        }
        
        echo "Memproses: '{$oldWilayah->nama}' (ID: $oldId) => '{$newWilayah->nama}' (ID: $newId)\n";
        
        // Update Sekolah
        $sekolahCount = Sekolah::where('wilayah_id', $oldId)->count();
        if ($sekolahCount > 0) {
            Sekolah::where('wilayah_id', $oldId)->update(['wilayah_id' => $newId]);
            echo "  - Updated $sekolahCount sekolah\n";
        }
        
        // Update PelaksanaanAsesmen
        $asesmenCount = PelaksanaanAsesmen::where('wilayah_id', $oldId)->count();
        if ($asesmenCount > 0) {
            PelaksanaanAsesmen::where('wilayah_id', $oldId)->update(['wilayah_id' => $newId]);
            echo "  - Updated $asesmenCount pelaksanaan asesmen\n";
        }
        
        // Hapus wilayah lama
        $oldWilayah->delete();
        echo "  - Deleted wilayah '{$oldWilayah->nama}' (ID: $oldId)\n";
        echo "\n";
    }
    
    DB::commit();
    echo "\n=== SELESAI! Semua duplikat berhasil dibersihkan ===\n";
    
    // Tampilkan wilayah yang tersisa
    echo "\n=== WILAYAH YANG TERSISA ===\n\n";
    $remaining = Wilayah::orderBy('id')->get();
    foreach ($remaining as $w) {
        echo "ID: {$w->id} | Nama: {$w->nama}\n";
    }
    echo "\nTotal: " . $remaining->count() . " wilayah\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n!!! ERROR: " . $e->getMessage() . "\n";
    echo "Rollback dilakukan, tidak ada perubahan.\n";
}
