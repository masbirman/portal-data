<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DAFTAR WILAYAH ===\n\n";

$wilayahs = \App\Models\Wilayah::orderBy('id')->get(['id', 'nama']);

foreach ($wilayahs as $w) {
    echo sprintf("ID: %d | Nama: %s\n", 
        $w->id, 
        $w->nama
    );
}

echo "\n=== TOTAL: " . $wilayahs->count() . " wilayah ===\n";

// Check for duplicates (case-insensitive)
echo "\n=== CEK DUPLIKAT (case-insensitive) ===\n\n";
$grouped = $wilayahs->groupBy(function($item) {
    return strtolower($item->nama);
});

foreach ($grouped as $key => $items) {
    if ($items->count() > 1) {
        echo "DUPLIKAT DITEMUKAN: '$key'\n";
        foreach ($items as $item) {
            echo "  - ID: {$item->id}, Nama: '{$item->nama}'\n";
        }
        echo "\n";
    }
}
