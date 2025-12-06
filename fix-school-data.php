<?php
/**
 * Script untuk memperbaiki data sekolah dari CSV Kemendikdasmen
 * Strategi:
 * 1. Match by exact name + wilayah - update NPSN, alamat, koordinat
 * 2. Match by NPSN - update alamat & koordinat
 * 3. Fuzzy match by name + wilayah (score >= 90%)
 *
 * Jalankan: php fix-school-data.php
 * Dry run:  php fix-school-data.php --dry-run
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sekolah;
use App\Models\Wilayah;

$dryRun = in_array('--dry-run', $argv);
if ($dryRun) {
    echo "=== DRY RUN MODE - No changes will be saved ===\n\n";
}

$csvDir = __DIR__ . '/kabupaten_csv';
$files = glob($csvDir . '/*.csv');

// Load wilayah mapping
$wilayahMap = [];
$wilayahs = Wilayah::all();
foreach ($wilayahs as $w) {
    $nama = strtolower($w->nama);
    $wilayahMap[$nama] = $w->id;
    $wilayahMap['kab. ' . $nama] = $w->id;
    $wilayahMap['kota ' . $nama] = $w->id;
    $wilayahMap['kabupaten ' . $nama] = $w->id;
}

$relevantBentuk = ['SD', 'SMP', 'SMA', 'SMK', 'SDLB', 'SMPLB', 'SMALB', 'SLB', 'PKBM'];

// Load all CSV data
echo "Loading CSV data...\n";
$csvData = [];
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

        $kabupaten = $get('kabupaten');
        $kabLower = strtolower(trim($kabupaten));
        $wilayahId = $wilayahMap[$kabLower] ?? null;
        if (!$wilayahId) {
            $cleaned = preg_replace('/^(kab\.|kota|kabupaten)\s*/i', '', $kabLower);
            $wilayahId = $wilayahMap[trim($cleaned)] ?? null;
        }
        if (!$wilayahId) continue;

        $npsn = $get('npsn');
        $record = [
            'npsn' => $npsn,
            'nama' => $get('nama'),
            'nama_normalized' => normalizeSchoolName($get('nama')),
            'alamat' => $get('alamat'),
            'latitude' => $get('latitude'),
            'longitude' => $get('longitude'),
            'wilayah_id' => $wilayahId,
        ];

        $csvData[] = $record;
        if ($npsn) {
            $csvByNpsn[$npsn] = $record;
        }
    }
    fclose($handle);
}

echo "Loaded " . count($csvData) . " records from CSV\n";
echo "Unique NPSN: " . count($csvByNpsn) . "\n\n";

$stats = [
    'total_db' => 0,
    'matched_by_name' => 0,
    'matched_by_npsn' => 0,
    'matched_by_fuzzy' => 0,
    'updated' => 0,
    'no_match' => 0,
];
$updates = [];
$noMatch = [];

echo "Processing schools in database...\n";
$schools = Sekolah::withoutGlobalScopes()->get();
$stats['total_db'] = $schools->count();

foreach ($schools as $school) {
    $matched = false;
    $csvRecord = null;
    $matchType = null;
    $dbNameNorm = normalizeSchoolName($school->nama);

    // Strategy 1: Match by exact normalized name + wilayah
    foreach ($csvData as $csv) {
        if ($csv['wilayah_id'] == $school->wilayah_id && $csv['nama_normalized'] == $dbNameNorm) {
            $csvRecord = $csv;
            $matchType = 'exact_name';
            $stats['matched_by_name']++;
            $matched = true;
            break;
        }
    }

    // Strategy 2: Match by NPSN
    if (!$matched && $school->npsn && isset($csvByNpsn[$school->npsn])) {
        $csvRecord = $csvByNpsn[$school->npsn];
        $matchType = 'npsn';
        $stats['matched_by_npsn']++;
        $matched = true;
    }

    // Strategy 3: DISABLED - Fuzzy matching too risky
    // Example: SDN 13 DAMPELAS -> SDN 3 DAMPELAS (wrong!)
    if (false && !$matched) {
        $bestMatch = null;
        $bestScore = 0;
        $dbPrefix = getJenjangPrefix($dbNameNorm);

        foreach ($csvData as $csv) {
            if ($csv['wilayah_id'] != $school->wilayah_id) continue;

            // Must have same jenjang prefix (SD, SMP, SMA, SMK, SLB)
            $csvPrefix = getJenjangPrefix($csv['nama_normalized']);
            if ($dbPrefix != $csvPrefix) continue;

            similar_text($dbNameNorm, $csv['nama_normalized'], $score);
            if ($score >= 95 && $score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $csv;
            }
        }
        if ($bestMatch) {
            $csvRecord = $bestMatch;
            $matchType = 'fuzzy_' . round($bestScore);
            $stats['matched_by_fuzzy']++;
            $matched = true;
        }
    }

    if (!$matched) {
        $stats['no_match']++;
        $noMatch[] = ['id' => $school->id, 'nama' => $school->nama, 'npsn' => $school->npsn];
        continue;
    }

    // Check what needs to be updated
    $changes = [];

    if ($csvRecord['npsn'] && $school->npsn != $csvRecord['npsn']) {
        $changes['npsn'] = ['old' => $school->npsn, 'new' => $csvRecord['npsn']];
    }

    $dbAlamat = strtoupper(trim($school->alamat ?? ''));
    $csvAlamat = strtoupper(trim($csvRecord['alamat'] ?? ''));
    if ($csvRecord['alamat'] && $dbAlamat != $csvAlamat) {
        $changes['alamat'] = ['old' => $school->alamat, 'new' => $csvRecord['alamat']];
    }

    if ($csvRecord['latitude'] && $csvRecord['longitude']) {
        $latDiff = abs((float)($school->latitude ?? 0) - (float)$csvRecord['latitude']);
        $lngDiff = abs((float)($school->longitude ?? 0) - (float)$csvRecord['longitude']);
        if ($latDiff > 0.0001 || $lngDiff > 0.0001 || !$school->latitude) {
            $changes['coordinates'] = [
                'old' => [$school->latitude, $school->longitude],
                'new' => [$csvRecord['latitude'], $csvRecord['longitude']]
            ];
        }
    }

    if (!empty($changes)) {
        $updates[] = [
            'id' => $school->id,
            'nama_db' => $school->nama,
            'nama_csv' => $csvRecord['nama'],
            'match_type' => $matchType,
            'changes' => $changes,
        ];

        if (!$dryRun) {
            if (isset($changes['npsn'])) $school->npsn = $changes['npsn']['new'];
            if (isset($changes['alamat'])) $school->alamat = $changes['alamat']['new'];
            if (isset($changes['coordinates'])) {
                $school->latitude = $changes['coordinates']['new'][0];
                $school->longitude = $changes['coordinates']['new'][1];
            }
            $school->save();
        }
        $stats['updated']++;
    }
}

echo "\n========== SUMMARY ==========\n";
echo "Total schools in DB: {$stats['total_db']}\n";
echo "Matched by exact name: {$stats['matched_by_name']}\n";
echo "Matched by NPSN: {$stats['matched_by_npsn']}\n";
echo "Matched by fuzzy: {$stats['matched_by_fuzzy']}\n";
echo "Will be updated: {$stats['updated']}\n";
echo "No match: {$stats['no_match']}\n";

if (!empty($updates)) {
    echo "\n========== SAMPLE UPDATES (first 30) ==========\n";
    foreach (array_slice($updates, 0, 30) as $u) {
        echo "\n[{$u['id']}] {$u['nama_db']}";
        if ($u['nama_db'] != $u['nama_csv']) echo " -> {$u['nama_csv']}";
        echo " ({$u['match_type']})\n";
        foreach ($u['changes'] as $field => $change) {
            if ($field === 'coordinates') {
                echo "  $field: [{$change['old'][0]}, {$change['old'][1]}] -> [{$change['new'][0]}, {$change['new'][1]}]\n";
            } else {
                echo "  $field: {$change['old']} -> {$change['new']}\n";
            }
        }
    }
}

$ts = date('Y-m-d_His');
file_put_contents(__DIR__ . "/storage/logs/school_updates_$ts.json", json_encode($updates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__ . "/storage/logs/school_no_match_$ts.json", json_encode($noMatch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n\nReports saved to storage/logs/\n";

if ($dryRun) echo "\n=== DRY RUN - Run without --dry-run to apply ===\n";

function normalizeSchoolName(string $name): string
{
    $name = strtoupper(trim($name));
    $name = preg_replace('/\s+/', ' ', $name);

    $replacements = [
        'SD NEGERI' => 'SDN', 'SD INPRES' => 'SDI', 'SD INP' => 'SDI',
        'SMP NEGERI' => 'SMPN', 'SMA NEGERI' => 'SMAN', 'SMK NEGERI' => 'SMKN',
        'SLB NEGERI' => 'SLBN', 'SATU ATAP' => 'SATAP', 'SD KECIL' => 'SDK', 'SD K ' => 'SDK ',
    ];

    foreach ($replacements as $search => $replace) {
        $name = str_replace($search, $replace, $name);
    }
    return $name;
}

function getJenjangPrefix(string $normalizedName): string
{
    if (preg_match('/^(SDN|SDI|SDK|SD)\b/', $normalizedName)) return 'SD';
    if (preg_match('/^SMPN?\b/', $normalizedName)) return 'SMP';
    if (preg_match('/^SMAN?\b/', $normalizedName)) return 'SMA';
    if (preg_match('/^SMKN?\b/', $normalizedName)) return 'SMK';
    if (preg_match('/^SLBN?\b/', $normalizedName)) return 'SLB';
    if (preg_match('/^PKBM\b/', $normalizedName)) return 'PKBM';
    return '';
}
