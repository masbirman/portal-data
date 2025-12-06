<?php
/**
 * Script untuk cek perbedaan data antara database dan CSV scrapping
 * Jalankan: php check-data-mismatch.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sekolah;
use App\Services\SchoolMatchingService;

$csvDir = __DIR__ . '/kabupaten_csv';
$files = glob($csvDir . '/*.csv');

$matchingService = new SchoolMatchingService(80);

// Load wilayah mapping
$wilayahMap = [];
$wilayahs = \App\Models\Wilayah::all();
foreach ($wilayahs as $w) {
    $nama = strtolower($w->nama);
    $wilayahMap[$nama] = $w->id;
    $wilayahMap['kab. ' . $nama] = $w->id;
    $wilayahMap['kota ' . $nama] = $w->id;
    $wilayahMap['kabupaten ' . $nama] = $w->id;
}

$mismatches = [];
$total = 0;

foreach ($files as $file) {
    echo "Processing: " . basename($file) . "\n";

    $handle = fopen($file, 'r');
    $header = fgetcsv($handle);
    $columns = array_flip($header);

    while (($row = fgetcsv($handle)) !== false) {
        $get = fn($key) => isset($columns[$key]) && isset($row[$columns[$key]])
            ? trim($row[$columns[$key]]) : null;

        $csvNpsn = $get('npsn');
        $csvNama = $get('nama');
        $csvAlamat = $get('alamat');
        $csvKabupaten = $get('kabupaten');
        $csvBentuk = $get('bentuk_pendidikan');
        $csvLat = $get('latitude');
        $csvLng = $get('longitude');

        // Skip non-relevant
        $relevant = ['SD', 'SMP', 'SMA', 'SMK', 'SDLB', 'SMPLB', 'SMALB', 'SLB', 'PKBM'];
        if (!in_array(strtoupper($csvBentuk), $relevant)) continue;

        // Find wilayah
        $kabLower = strtolower(trim($csvKabupaten));
        $wilayahId = $wilayahMap[$kabLower] ?? null;
        if (!$wilayahId) {
            $cleaned = preg_replace('/^(kab\.|kota|kabupaten)\s*/i', '', $kabLower);
            $wilayahId = $wilayahMap[trim($cleaned)] ?? null;
        }
        if (!$wilayahId) continue;

        // Find matching school in DB
        $jenjangId = $matchingService->mapJenjangId($csvBentuk);
        $match = $matchingService->findMatch($csvNama, $wilayahId, $jenjangId);

        if (!$match) continue;

        $school = $match['school'];
        $total++;

        // Check for mismatches
        $diffs = [];

        // NPSN mismatch
        if ($school->npsn && $csvNpsn && $school->npsn != $csvNpsn) {
            $diffs['npsn'] = ['db' => $school->npsn, 'csv' => $csvNpsn];
        }

        // Alamat mismatch (normalize for comparison)
        $dbAlamat = strtoupper(trim($school->alamat ?? ''));
        $csvAlamatNorm = strtoupper(trim($csvAlamat ?? ''));
        if ($dbAlamat && $csvAlamatNorm && $dbAlamat != $csvAlamatNorm) {
            // Check if significantly different (not just formatting)
            similar_text($dbAlamat, $csvAlamatNorm, $percent);
            if ($percent < 80) {
                $diffs['alamat'] = ['db' => $school->alamat, 'csv' => $csvAlamat];
            }
        }

        // Coordinates mismatch
        if ($school->latitude && $csvLat) {
            $latDiff = abs((float)$school->latitude - (float)$csvLat);
            $lngDiff = abs((float)$school->longitude - (float)$csvLng);
            if ($latDiff > 0.001 || $lngDiff > 0.001) { // ~100m difference
                $diffs['coordinates'] = [
                    'db' => [$school->latitude, $school->longitude],
                    'csv' => [$csvLat, $csvLng]
                ];
            }
        }

        if (!empty($diffs)) {
            $mismatches[] = [
                'id' => $school->id,
                'nama_db' => $school->nama,
                'nama_csv' => $csvNama,
                'match_score' => $match['score'],
                'differences' => $diffs
            ];
        }
    }

    fclose($handle);
}

echo "\n========== MISMATCH REPORT ==========\n";
echo "Total matched schools: $total\n";
echo "Schools with mismatches: " . count($mismatches) . "\n\n";

foreach ($mismatches as $m) {
    echo "-------------------------------------------\n";
    echo "ID: {$m['id']} | {$m['nama_db']}\n";
    echo "CSV: {$m['nama_csv']} (score: {$m['match_score']}%)\n";

    foreach ($m['differences'] as $field => $diff) {
        if ($field === 'coordinates') {
            echo "  $field:\n";
            echo "    DB:  [{$diff['db'][0]}, {$diff['db'][1]}]\n";
            echo "    CSV: [{$diff['csv'][0]}, {$diff['csv'][1]}]\n";
        } else {
            echo "  $field:\n";
            echo "    DB:  {$diff['db']}\n";
            echo "    CSV: {$diff['csv']}\n";
        }
    }
}

// Save to file
$outputFile = __DIR__ . '/storage/logs/data_mismatches_' . date('Y-m-d_His') . '.json';
file_put_contents($outputFile, json_encode($mismatches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n\nFull report saved to: $outputFile\n";
