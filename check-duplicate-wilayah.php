<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Wilayah;

echo "=== Checking Wilayah for Tojo Unauna ===\n";

$wilayahs = Wilayah::where('nama', 'like', '%Tojo%')->get();

foreach ($wilayahs as $w) {
    echo "ID: {$w->id}, Nama: '{$w->nama}', Sekolah Count: " . $w->sekolah()->count() . ", Asesmen Count: " . $w->pelaksanaanAsesmen()->count() . "\n";
}
