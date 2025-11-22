<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sekolah;
use App\Models\PelaksanaanAsesmen;
use App\Models\JenjangPendidikan;
use App\Models\Wilayah;
use App\Models\SiklusAsesmen;

echo "=== Data Counts ===\n";
echo "Sekolah: " . Sekolah::count() . "\n";
echo "PelaksanaanAsesmen: " . PelaksanaanAsesmen::count() . "\n";
echo "JenjangPendidikan: " . JenjangPendidikan::count() . "\n";
echo "Wilayah: " . Wilayah::count() . "\n";

echo "\n=== Sample Sekolah (First 5) ===\n";
foreach (Sekolah::take(5)->get() as $sekolah) {
    echo "ID: {$sekolah->id}, Nama: {$sekolah->nama}, JenjangID: {$sekolah->jenjang_pendidikan_id}, WilayahID: {$sekolah->wilayah_id}, Tahun: " . json_encode($sekolah->tahun) . "\n";
}

echo "\n=== Sample PelaksanaanAsesmen (First 5) ===\n";
foreach (PelaksanaanAsesmen::take(5)->get() as $pa) {
    echo "ID: {$pa->id}, SekolahID: {$pa->sekolah_id}, SiklusID: {$pa->siklus_asesmen_id}, WilayahID: {$pa->wilayah_id}\n";
}

echo "\n=== Siklus Asesmen ===\n";
foreach (SiklusAsesmen::all() as $siklus) {
    echo "ID: {$siklus->id}, Tahun: {$siklus->tahun}, Nama: {$siklus->nama}\n";
}
