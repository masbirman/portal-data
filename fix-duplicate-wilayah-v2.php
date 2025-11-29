<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Wilayah;
use App\Models\Sekolah;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== SCRIPT PEMBERSIHAN WILAYAH DUPLIKAT (DINAMIS) ===\n\n";

// Function to normalize wilayah name (same as in SekolahImport)
function normalizeWilayahName($name)
{
    $name = trim($name);
    
    // Replace "Kab." or "Kab " with "Kabupaten "
    $name = preg_replace('/^Kab\.?\s+/i', 'Kabupaten ', $name);
    
    // Normalize multiple spaces to single space
    $name = preg_replace('/\s+/', ' ', $name);
    
    // Fix specific cases
    // "Tolitoli" => "Toli-Toli"
    if (stripos($name, 'Tolitoli') !== false) {
        $name = str_ireplace('Tolitoli', 'Toli-Toli', $name);
    }
    
    // "Tojo Unauna" or "Tojo Una-una" => "Tojo Una-Una"
    if (stripos($name, 'Tojo Una') !== false) {
        $name = preg_replace('/Tojo\s+Una[-\s]?una/i', 'Tojo Una-Una', $name);
    }
    
    // Apply title case
    return Str::title($name);
}

// Get all wilayah and group by normalized name
$allWilayah = Wilayah::orderBy('id')->get();
$grouped = [];

foreach ($allWilayah as $wilayah) {
    $normalized = strtolower(normalizeWilayahName($wilayah->nama));
    if (!isset($grouped[$normalized])) {
        $grouped[$normalized] = [];
    }
    $grouped[$normalized][] = $wilayah;
}

echo "=== DETEKSI DUPLIKAT ===\n\n";

$duplicateMapping = [];
$duplicatesFound = false;

foreach ($grouped as $normalizedName => $wilayahs) {
    if (count($wilayahs) > 1) {
        $duplicatesFound = true;
        echo "Duplikat ditemukan untuk: '$normalizedName'\n";
        
        // Keep the first one (lowest ID), mark others for deletion
        $keepWilayah = $wilayahs[0];
        echo "  KEEP: ID {$keepWilayah->id} - '{$keepWilayah->nama}'\n";
        
        for ($i = 1; $i < count($wilayahs); $i++) {
            $deleteWilayah = $wilayahs[$i];
            echo "  DELETE: ID {$deleteWilayah->id} - '{$deleteWilayah->nama}'\n";
            $duplicateMapping[$deleteWilayah->id] = $keepWilayah->id;
        }
        echo "\n";
    }
}

if (!$duplicatesFound) {
    echo "Tidak ada duplikat ditemukan!\n";
    exit(0);
}

echo "\n=== MEMULAI PEMBERSIHAN ===\n\n";

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
        $normalized = normalizeWilayahName($w->nama);
        echo "ID: {$w->id} | Nama: {$w->nama}";
        if ($w->nama !== $normalized) {
            echo " => AKAN DINORMALISASI: '$normalized'";
        }
        echo "\n";
    }
    echo "\nTotal: " . $remaining->count() . " wilayah\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n!!! ERROR: " . $e->getMessage() . "\n";
    echo "Rollback dilakukan, tidak ada perubahan.\n";
}
