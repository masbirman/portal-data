<?php

use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use App\Models\PelaksanaanAsesmen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Resetting Data Jenjang & Sekolah...\n";

Schema::disableForeignKeyConstraints();

// Truncate tables
echo "   ↳ Truncating tables...\n";
PelaksanaanAsesmen::truncate();
Sekolah::truncate();
JenjangPendidikan::truncate();

Schema::enableForeignKeyConstraints();

echo "✅ Tables truncated.\n";

// Run Seeder
echo "   ↳ Seeding Jenjang Pendidikan...\n";
$jenjangs = [
    'SMA',
    'SMK',
    'SMP',
    'SD',
    'SMALB',
    'SMPLB',
    'SDLB',
    'PAKET C',
    'PAKET B',
    'PAKET A',
];

foreach ($jenjangs as $jenjang) {
    JenjangPendidikan::create([
        'kode' => $jenjang,
        'nama' => $jenjang,
    ]);
}

echo "✅ Jenjang Pendidikan seeded successfully!\n";
