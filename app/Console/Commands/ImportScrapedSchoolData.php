<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use App\Models\Wilayah;
use App\Services\SchoolMatchingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportScrapedSchoolData extends Command
{
    protected $signature = 'school:import-scraped
                            {path : Path to CSV file or directory containing CSV files}
                            {--threshold=80 : Minimum matching score (0-100)}
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Import scraped school data from CSV files to enrich existing school records';

    protected SchoolMatchingService $matchingService;
    protected array $stats = [
        'total' => 0,
        'matched' => 0,
        'updated' => 0,
        'skipped' => 0,
        'no_match' => 0,
    ];
    protected array $skippedRecords = [];
    protected array $unmatchedRecords = [];

    public function handle(): int
    {
        $path = $this->argument('path');
        $threshold = (int) $this->option('threshold');
        $dryRun = $this->option('dry-run');

        $this->matchingService = new SchoolMatchingService($threshold);

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved');
        }

        $this->info("Using matching threshold: {$threshold}%");

        // Get CSV files
        $files = $this->getCsvFiles($path);
        if (empty($files)) {
            $this->error("No CSV files found at: {$path}");
            return Command::FAILURE;
        }

        $this->info('Found ' . count($files) . ' CSV file(s)');


        // Load wilayah mapping
        $wilayahMap = $this->loadWilayahMap();

        // Process each file
        foreach ($files as $file) {
            $this->processFile($file, $wilayahMap, $dryRun);
        }

        // Display summary
        $this->displaySummary();

        // Save unmatched records for review
        if (!empty($this->unmatchedRecords)) {
            $this->saveUnmatchedRecords();
        }

        return Command::SUCCESS;
    }

    protected function getCsvFiles(string $path): array
    {
        $fullPath = base_path($path);

        if (is_file($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'csv') {
            return [$fullPath];
        }

        if (is_dir($fullPath)) {
            return glob($fullPath . '/*.csv');
        }

        return [];
    }

    protected function loadWilayahMap(): array
    {
        $wilayah = Wilayah::all();
        $map = [];

        foreach ($wilayah as $w) {
            // Create variations for matching
            $nama = strtolower($w->nama);
            $map[$nama] = $w->id;
            $map['kab. ' . $nama] = $w->id;
            $map['kota ' . $nama] = $w->id;
            $map['kabupaten ' . $nama] = $w->id;
        }

        return $map;
    }

    protected function findWilayahId(string $kabupaten, array $wilayahMap): ?int
    {
        $kabupaten = strtolower(trim($kabupaten));

        // Direct match
        if (isset($wilayahMap[$kabupaten])) {
            return $wilayahMap[$kabupaten];
        }

        // Try without prefix
        $cleaned = preg_replace('/^(kab\.|kota|kabupaten)\s*/i', '', $kabupaten);
        $cleaned = trim($cleaned);

        if (isset($wilayahMap[$cleaned])) {
            return $wilayahMap[$cleaned];
        }

        // Fuzzy match
        foreach ($wilayahMap as $name => $id) {
            if (str_contains($kabupaten, $name) || str_contains($name, $cleaned)) {
                return $id;
            }
        }

        return null;
    }


    protected function processFile(string $file, array $wilayahMap, bool $dryRun): void
    {
        $this->info("\nProcessing: " . basename($file));

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("Cannot open file: {$file}");
            return;
        }

        // Read header
        $header = fgetcsv($handle);
        if (!$header) {
            $this->error("Cannot read header from: {$file}");
            fclose($handle);
            return;
        }

        // Map column indices
        $columns = array_flip($header);

        $bar = $this->output->createProgressBar();
        $bar->start();

        while (($row = fgetcsv($handle)) !== false) {
            $bar->advance();
            $this->stats['total']++;

            $data = $this->parseRow($row, $columns);
            if (!$data) {
                $this->stats['skipped']++;
                continue;
            }

            // Skip non-relevant bentuk_pendidikan
            if (!$this->isRelevantBentuk($data['bentuk_pendidikan'])) {
                $this->stats['skipped']++;
                $this->skippedRecords[] = [
                    'nama' => $data['nama'],
                    'reason' => "Bentuk pendidikan tidak relevan: {$data['bentuk_pendidikan']}",
                ];
                continue;
            }

            // Find wilayah
            $wilayahId = $this->findWilayahId($data['kabupaten'], $wilayahMap);
            if (!$wilayahId) {
                $this->stats['skipped']++;
                $this->skippedRecords[] = [
                    'nama' => $data['nama'],
                    'reason' => "Wilayah tidak ditemukan: {$data['kabupaten']}",
                ];
                continue;
            }

            // Find matching school
            $jenjangId = $this->matchingService->mapJenjangId($data['bentuk_pendidikan']);
            $match = $this->matchingService->findMatch($data['nama'], $wilayahId, $jenjangId);

            if (!$match) {
                $this->stats['no_match']++;
                $this->unmatchedRecords[] = [
                    'npsn' => $data['npsn'],
                    'nama' => $data['nama'],
                    'bentuk' => $data['bentuk_pendidikan'],
                    'kabupaten' => $data['kabupaten'],
                ];
                continue;
            }

            $this->stats['matched']++;

            // Update school record
            if (!$dryRun) {
                $updated = $this->updateSchool($match['school'], $data);
                if ($updated) {
                    $this->stats['updated']++;
                }
            } else {
                $this->stats['updated']++;
            }
        }

        $bar->finish();
        $this->newLine();
        fclose($handle);
    }


    protected function parseRow(array $row, array $columns): ?array
    {
        $get = fn($key) => isset($columns[$key]) && isset($row[$columns[$key]])
            ? trim($row[$columns[$key]])
            : null;

        $nama = $get('nama');
        if (!$nama) {
            return null;
        }

        return [
            'npsn' => $get('npsn'),
            'nama' => $nama,
            'alamat' => $get('alamat'),
            'kecamatan' => $get('kecamatan'),
            'kabupaten' => $get('kabupaten'),
            'bentuk_pendidikan' => $get('bentuk_pendidikan'),
            'status_sekolah' => $get('status_sekolah'),
            'latitude' => $get('latitude'),
            'longitude' => $get('longitude'),
        ];
    }

    protected function isRelevantBentuk(string $bentuk): bool
    {
        $relevant = ['SD', 'SMP', 'SMA', 'SMK', 'SDLB', 'SMPLB', 'SMALB', 'SLB', 'PKBM'];
        return in_array(strtoupper($bentuk), $relevant);
    }

    protected function updateSchool(Sekolah $school, array $data): bool
    {
        $updated = false;

        // Only update NULL fields (safe update)
        if ($school->npsn === null && $data['npsn']) {
            $school->npsn = $data['npsn'];
            $updated = true;
        }

        if ($school->alamat === null && $data['alamat']) {
            $school->alamat = $data['alamat'];
            $updated = true;
        }

        if ($school->latitude === null && $data['latitude']) {
            $school->latitude = (float) $data['latitude'];
            $updated = true;
        }

        if ($school->longitude === null && $data['longitude']) {
            $school->longitude = (float) $data['longitude'];
            $updated = true;
        }

        // Update status if different and current is NULL
        if ($school->status_sekolah === null && $data['status_sekolah']) {
            $school->status_sekolah = $this->matchingService->mapStatus($data['status_sekolah']);
            $updated = true;
        }

        if ($updated) {
            $school->save();
        }

        return $updated;
    }

    protected function displaySummary(): void
    {
        $this->newLine();
        $this->info('========== IMPORT SUMMARY ==========');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Records', $this->stats['total']],
                ['Matched', $this->stats['matched']],
                ['Updated', $this->stats['updated']],
                ['Skipped', $this->stats['skipped']],
                ['No Match Found', $this->stats['no_match']],
            ]
        );
    }

    protected function saveUnmatchedRecords(): void
    {
        $filename = storage_path('logs/unmatched_schools_' . date('Y-m-d_His') . '.csv');

        $handle = fopen($filename, 'w');
        fputcsv($handle, ['npsn', 'nama', 'bentuk_pendidikan', 'kabupaten']);

        foreach ($this->unmatchedRecords as $record) {
            fputcsv($handle, [
                $record['npsn'],
                $record['nama'],
                $record['bentuk'],
                $record['kabupaten'],
            ]);
        }

        fclose($handle);

        $this->warn("Unmatched records saved to: {$filename}");
    }
}
