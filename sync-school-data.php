<?php
/**
 * Script untuk sinkronisasi data sekolah dari CSV Kemendikdasmen
 * Menggunakan NPSN sebagai primary key untuk matching
 *
 * Jalankan: php sync-school-data.php
 * Dry run:  php sync-school-data.php --dry-run
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sekolah;
use App\Models\Wilayah;

$dryRun = in_array('--dry-run', $argv);
if ($dryRun) {
    echo "=== DRY RUN MODE ===\n\n";
}

$csvDir = __DIR__ . '/kabupaten_csv';
$files = glob($csvDir . '/*.csv');

// Load wilayah mapping
$wilayahMap = [];
foreach (Wilayah::all() as $w) {
    $nama = strtolower($w->nama);
    $wilayahMap[$nama] = $w->id;
    $wilayahMap['kab. ' . $nama] = $w->id;
    $wilayahMap['kota ' . $nama] = $w->id;
    $wilayahMap['kabupaten ' . $nama] = $w->id;
}

$relevantBentuk = ['SD', 'SMP', 'SMA', 'SMK', 'SDLB', 'SMPLB', 'SMALB', 'SLB', 'PKBM'];

// Load all CSV data indexed by NPSN
echo "Loading CSV data...\n";
$csvByNpsn = [];

foreach ($files as $file) {
    $handle = fopen($file, 'r');
    $header = fgetcsv($handle);
    $columns = array_flip($header);

    while (($row = fgetcsv($handle)) !== false) {
        $get = fn($key) => isset($columns[$key]) && isset($row[$columns[$key]])
            ? trim($row[$columns[$key]]) : null;

        $bentuk = strtoupper($get('bentuk_pendidikan'));
        if (!in_array($bentuk, $relevantBentuk)) continue;

        $npsn = $get('npsn');
        if (!$npsn) continue;

        $kabupaten = $get('kabupaten');
        $kabLower = strtolower(trim($kabupaten));
        $wilayahId = $wilayahMap[$kabLower] ?? null;
        if (!$wilayahId) {
            $cleaned = preg_replace('/^(kab\.|kota|kabupaten)\s*/i', '', $kabLower);
            $wilayahId = $wilayahMap[trim($cleaned)] ?? null;
        }

        $csvByNpsn[$npsn] = [
            'npsn' => $npsn,
            'nama' => $get('nama'),
            'alamat' => $get('alamat'),
            'latitude' => $get('latitude'),
            'longitude' => $get('longitude'),
            'wilayah_id' => $wilayahId,
            'bentuk' => $bentuk,
        ];
    }
    fclose($handle);
}

echo "Loaded " . count($csvByNpsn) . " unique NPSN from CSV\n\n";

// Get all schools from DB that have NPSN
$schools = Sekolah::withoutGlobalScopes()->whereNotNull('npsn')->where('npsn', '!=', '')->get();
echo "Schools in DB with NPSN: " . $schools->count() . "\n\n";

$stats = ['matched' => 0, 'updated' => 0, 'not_in_csv' => 0];
$updates = [];
$notInCsv = [];

foreach ($schools as $school) {
    $npsn = $school->npsn;

    if (!isset($csvByNpsn[$npsn])) {
        $stats['not_in_csv']++;
        $notInCsv[] = ['id' => $school->id, 'nama' => $school->nama, 'npsn' => $npsn];
        continue;
    }

    $csv = $csvByNpsn[$npsn];
    $stats['matched']++;

    // Check differences and update
    $changes = [];

    // Update alamat if different
    if ($csv['alamat'] && strtoupper(trim($school->alamat ?? '')) != strtoupper(trim($csv['alamat']))) {
        $changes['alamat'] = ['old' => $school->alamat, 'new' => $csv['alamat']];
        if (!$dryRun) $school->alamat = $csv['alamat'];
    }

    // Update coordinates if different
    if ($csv['latitude'] && $csv['longitude']) {
        $latDiff = abs((float)($school->latitude ?? 0) - (float)$csv['latitude']);
        $lngDiff = abs((float)($school->longitude ?? 0) - (float)$csv['longitude']);
        if ($latDiff > 0.0001 || $lngDiff > 0.0001 || !$school->latitude) {
            $changes['coordinates'] = [
                'old' => [$school->latitude, $school->longitude],
                'new' => [$csv['latitude'], $csv['longitude']]
            ];
            if (!$dryRun) {
                $school->latitude = $csv['latitude'];
                $school->longitude = $csv['longitude'];
            }
        }
    }

    if (!empty($changes)) {
        $updates[] = [
            'id' => $school->id,
            'nama_db' => $school->nama,
            'nama_csv' => $csv['nama'],
            'npsn' => $npsn,
            'changes' => $changes,
        ];
        if (!$dryRun) {
            $school->save();
        }
        $stats['updated']++;
    }
}

echo "========== SUMMARY ==========\n";
echo "Matched by NPSN: {$stats['matched']}\n";
echo "Updated: {$stats['updated']}\n";
echo "NPSN not found in CSV: {$stats['not_in_csv']}\n";

if (!empty($updates)) {
    echo "\n========== UPDATES ==========\n";
    foreach ($updates as $u) {
        echo "\n[{$u['id']}] {$u['nama_db']} (NPSN: {$u['npsn']})\n";
        if ($u['nama_db'] != $u['nama_csv']) {
            echo "  CSV nama: {$u['nama_csv']}\n";
        }
        foreach ($u['changes'] as $field => $change) {
            if ($field === 'coordinates') {
                echo "  $field: [{$change['old'][0]}, {$change['old'][1]}] -> [{$change['new'][0]}, {$change['new'][1]}]\n";
            } else {
                echo "  $field: {$change['old']} -> {$change['new']}\n";
            }
        }
    }
}

if (!empty($notInCsv) && count($notInCsv) <= 50) {
    echo "\n========== NPSN NOT IN CSV (need manual check) ==========\n";
    foreach ($notInCsv as $s) {
        echo "[{$s['id']}] {$s['nama']} - NPSN: {$s['npsn']}\n";
    }
}

$ts = date('Y-m-d_His');
file_put_contents(__DIR__ . "/storage/logs/sync_updates_$ts.json", json_encode($updates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__ . "/storage/logs/sync_not_in_csv_$ts.json", json_encode($notInCsv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($dryRun) echo "\n=== DRY RUN - Run without --dry-run to apply ===\n";
