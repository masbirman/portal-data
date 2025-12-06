<?php
/**
 * Script untuk mengisi NPSN yang kosong dengan matching nama sekolah
 * Strategy:
 * 1. Exact match setelah normalisasi
 * 2. Fuzzy match dengan rules ketat (jenjang sama, nomor sama)
 *
 * Jalankan: php fix-missing-npsn.php
 * Dry run:  php fix-missing-npsn.php --dry-run
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sekolah;
use App\Models\Wilayah;

$dryRun = in_array('--dry-run', $argv);
if ($dryRun) echo "=== DRY RUN MODE ===\n\n";

$csvDir = __DIR__ . '/kabupaten_csv';
$files = glob($csvDir . '/*.csv');

// Load wilayah mapping
$wilayahMap = [];
foreach (Wilayah::all() as $w) {
    $nama = strtolower($w->nama);
    $namaNoHyphen = str_replace('-', '', $nama);

    // Add all variations
    $wilayahMap[$nama] = $w->id;
    $wilayahMap[$namaNoHyphen] = $w->id;
    $wilayahMap['kab. ' . $nama] = $w->id;
    $wilayahMap['kab. ' . $namaNoHyphen] = $w->id;
    $wilayahMap['kota ' . $nama] = $w->id;
    $wilayahMap['kota ' . $namaNoHyphen] = $w->id;
    $wilayahMap['kabupaten ' . $nama] = $w->id;
    $wilayahMap['kabupaten ' . $namaNoHyphen] = $w->id;
}

$relevantBentuk = ['SD', 'SMP', 'SMA', 'SMK', 'SDLB', 'SMPLB', 'SMALB', 'SLB', 'PKBM'];

// Load CSV data grouped by wilayah
echo "Loading CSV data...\n";
$csvByWilayah = [];

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
        if (!$wilayahId) continue;

        $nama = $get('nama');
        $normalized = normalizeSchoolName($nama);

        if (!isset($csvByWilayah[$wilayahId])) {
            $csvByWilayah[$wilayahId] = [];
        }

        $csvByWilayah[$wilayahId][$normalized] = [
            'npsn' => $npsn,
            'nama' => $nama,
            'alamat' => $get('alamat'),
            'latitude' => $get('latitude'),
            'longitude' => $get('longitude'),
        ];
    }
    fclose($handle);
}

$totalCsv = array_sum(array_map('count', $csvByWilayah));
echo "Loaded $totalCsv records from CSV\n\n";

// Get schools without NPSN
$schools = Sekolah::withoutGlobalScopes()
    ->where(function($q) {
        $q->whereNull('npsn')->orWhere('npsn', '');
    })
    ->get();

echo "Schools without NPSN: " . $schools->count() . "\n\n";

$stats = ['matched' => 0, 'not_matched' => 0];
$updates = [];
$notMatched = [];

foreach ($schools as $school) {
    $wilayahId = $school->wilayah_id;
    $dbNormalized = normalizeSchoolName($school->nama);

    $matched = false;
    $csv = null;
    $matchType = '';

    // Strategy 1: Exact match after normalization
    if (isset($csvByWilayah[$wilayahId][$dbNormalized])) {
        $csv = $csvByWilayah[$wilayahId][$dbNormalized];
        $matched = true;
        $matchType = 'exact';
    }

    // Strategy 2: Fuzzy match with strict rules
    if (!$matched && isset($csvByWilayah[$wilayahId])) {
        $dbPrefix = getJenjangPrefix($dbNormalized);
        $dbNumber = extractSchoolNumber($dbNormalized);
        $dbLocation = extractLocationName($dbNormalized);

        $bestMatch = null;
        $bestScore = 0;

        foreach ($csvByWilayah[$wilayahId] as $csvNorm => $csvData) {
            // Must have same jenjang prefix (SD, SMP, SMA, SMK, SLB)
            $csvPrefix = getJenjangPrefix($csvNorm);
            if ($dbPrefix && $csvPrefix && $dbPrefix != $csvPrefix) continue;

            // If both have numbers, they must match
            $csvNumber = extractSchoolNumber($csvNorm);
            if ($dbNumber && $csvNumber && $dbNumber != $csvNumber) continue;

            // If both have location names, check similarity
            $csvLocation = extractLocationName($csvNorm);
            if ($dbLocation && $csvLocation) {
                similar_text(strtoupper($dbLocation), strtoupper($csvLocation), $locScore);
                if ($locScore < 70) continue; // Location must be similar
            }

            // Calculate overall similarity
            similar_text($dbNormalized, $csvNorm, $score);

            // Require very high similarity (>= 90%) for safety
            if ($score >= 90 && $score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $csvData;
            }
        }

        if ($bestMatch) {
            $csv = $bestMatch;
            $matched = true;
            $matchType = 'fuzzy_' . round($bestScore);
        }
    }

    if ($matched) {
        $stats['matched']++;

        $updates[] = [
            'id' => $school->id,
            'nama_db' => $school->nama,
            'nama_csv' => $csv['nama'],
            'npsn' => $csv['npsn'],
            'alamat' => $csv['alamat'],
            'lat' => $csv['latitude'],
            'lng' => $csv['longitude'],
            'match_type' => $matchType,
        ];

        if (!$dryRun) {
            $school->npsn = $csv['npsn'];
            if ($csv['alamat']) $school->alamat = $csv['alamat'];
            if ($csv['latitude']) $school->latitude = $csv['latitude'];
            if ($csv['longitude']) $school->longitude = $csv['longitude'];
            $school->save();
        }
    } else {
        $stats['not_matched']++;
        $notMatched[] = [
            'id' => $school->id,
            'nama' => $school->nama,
            'nama_normalized' => $dbNormalized,
            'wilayah_id' => $wilayahId,
        ];
    }
}

echo "========== SUMMARY ==========\n";
echo "Matched: {$stats['matched']}\n";
echo "Not matched: {$stats['not_matched']}\n";

if (!empty($updates)) {
    echo "\n========== UPDATES (first 50) ==========\n";
    foreach (array_slice($updates, 0, 50) as $u) {
        echo "[{$u['id']}] {$u['nama_db']}";
        if ($u['nama_db'] != $u['nama_csv']) echo " -> {$u['nama_csv']}";
        echo " (NPSN: {$u['npsn']}, {$u['match_type']})\n";
    }
}

if (!empty($notMatched)) {
    echo "\n========== NOT MATCHED (first 50) ==========\n";
    foreach (array_slice($notMatched, 0, 50) as $s) {
        echo "[{$s['id']}] {$s['nama']}\n";
    }
}

$ts = date('Y-m-d_His');
file_put_contents(__DIR__ . "/storage/logs/npsn_updates_$ts.json", json_encode($updates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__ . "/storage/logs/npsn_not_matched_$ts.json", json_encode($notMatched, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($dryRun) echo "\n=== DRY RUN - Run without --dry-run to apply ===\n";

function normalizeSchoolName(string $name): string
{
    $name = strtoupper(trim($name));
    $name = preg_replace('/\s+/', ' ', $name);

    // Remove hyphens (TOLI-TOLI -> TOLITOLI, UNA-UNA -> UNAUNA)
    $name = str_replace('-', '', $name);

    $replacements = [
        'SD NEGERI' => 'SDN',
        'SDN INPRES' => 'SDI',
        'SD INPRES' => 'SDI',
        'SD INP' => 'SDI',
        'SMP NEGERI' => 'SMPN',
        'SMA NEGERI' => 'SMAN',
        'SMK NEGERI' => 'SMKN',
        'SLB NEGERI' => 'SLBN',
        'SATU ATAP' => 'SATAP',
        'SD KECIL' => 'SDK',
        'SMAS ' => 'SMA ',
        'SMKS ' => 'SMK ',
        'SD K ' => 'SDK ',
        'NEGERI SATAP' => 'SATAP',
        'SATAP NEGERI' => 'SATAP',
    ];

    foreach ($replacements as $search => $replace) {
        $name = str_replace($search, $replace, $name);
    }

    return preg_replace('/\s+/', ' ', trim($name));
}

function getJenjangPrefix(string $name): string
{
    // Order matters - check more specific patterns first
    if (preg_match('/^(SMKN|SMKS|SMK)\b/', $name)) return 'SMK';
    if (preg_match('/^(SMAN|SMAS|SMA)\b/', $name)) return 'SMA';
    if (preg_match('/^(SMPN|SMPS|SMP|SMPTK)\b/', $name)) return 'SMP';
    if (preg_match('/^(SMTK)\b/', $name)) return 'SMTK'; // Sekolah Menengah Teologi Kristen
    if (preg_match('/^(SDN|SDI|SDK|SDIT|SD)\b/', $name)) return 'SD';
    if (preg_match('/^(SLBN|SLB)\b/', $name)) return 'SLB';
    if (preg_match('/^PKBM\b/', $name)) return 'PKBM';
    return '';
}

function extractSchoolNumber(string $name): ?string
{
    // Match patterns like "SDN 1", "SMAN 2", "SMP NEGERI 14"
    if (preg_match('/\b(\d+)\b/', $name, $matches)) {
        return $matches[1];
    }
    return null;
}

function extractLocationName(string $name): ?string
{
    // Remove prefix and number, get location name
    // e.g., "SMAN 1 PALU" -> "PALU", "SDI BOMBAN" -> "BOMBAN"
    $name = preg_replace('/^(SDN|SDI|SDK|SDIT|SD|SMPN|SMP|SMAN|SMA|SMKN|SMK|SLBN|SLB|PKBM)\s*/i', '', $name);
    $name = preg_replace('/^(SATAP|NEGERI|INPRES|KECIL|TERPENCIL)\s*/i', '', $name);
    $name = preg_replace('/^\d+\s*/', '', $name);
    $name = trim($name);
    return $name ?: null;
}
