<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use App\Models\JenjangPendidikan;
use Illuminate\Console\Command;

class UpdateSekolahJenjang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sekolah:update-jenjang {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update jenjang_pendidikan_id for all sekolah based on their nama field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY-RUN mode - no changes will be made');
        }

        $this->info('Fetching jenjang pendidikan mapping...');
        
        // Get all jenjang pendidikan, ordered by ID to get the original ones first
        // Group by nama and take the first (lowest ID) for each unique name
        $allJenjang = JenjangPendidikan::orderBy('id')->get();
        $jenjangMap = $allJenjang->groupBy('nama')->map(function ($group) {
            return $group->first(); // Take the first (lowest ID) when there are duplicates
        });
        
        if ($jenjangMap->isEmpty()) {
            $this->error('No jenjang pendidikan found in database!');
            return 1;
        }

        $this->info('Available jenjang: ' . $jenjangMap->keys()->implode(', '));
        
        // Warn if there are duplicates
        if ($allJenjang->count() > $jenjangMap->count()) {
            $this->warn("Warning: Found {$allJenjang->count()} jenjang records but only {$jenjangMap->count()} unique names. Using lowest ID for duplicates.");
            
            // Show which ones are duplicates
            $duplicates = $allJenjang->groupBy('nama')->filter(function ($group) {
                return $group->count() > 1;
            });
            
            foreach ($duplicates as $nama => $group) {
                $ids = $group->pluck('id')->implode(', ');
                $this->warn("  - '{$nama}' has IDs: {$ids} (using ID {$group->first()->id})");
            }
        }
        
        $this->newLine();

        // Get all sekolah
        $sekolahAll = Sekolah::all();
        $this->info("Processing {$sekolahAll->count()} sekolah records...");
        $this->newLine();

        $stats = [
            'updated' => 0,
            'unchanged' => 0,
            'unknown' => 0,
        ];

        $changes = [];

        foreach ($sekolahAll as $sekolah) {
            $nama = strtoupper($sekolah->nama);
            $currentJenjangId = $sekolah->jenjang_pendidikan_id;
            $newJenjangId = null;
            $detectedJenjang = null;

            // Pattern matching to detect jenjang from nama
            if (preg_match('/\bSMA\b/', $nama)) {
                $detectedJenjang = 'SMA';
            } elseif (preg_match('/\bSMK\b/', $nama)) {
                $detectedJenjang = 'SMK';
            } elseif (preg_match('/\bSMP\b/', $nama)) {
                $detectedJenjang = 'SMP';
            } elseif (preg_match('/\bSD\b/', $nama)) {
                $detectedJenjang = 'SD';
            } elseif (preg_match('/\bSLB\b/', $nama)) {
                $detectedJenjang = 'SLB';
            } elseif (preg_match('/KESETARAAN|PAKET/', $nama)) {
                $detectedJenjang = 'Kesetaraan';
            }

            if ($detectedJenjang && isset($jenjangMap[$detectedJenjang])) {
                $newJenjangId = $jenjangMap[$detectedJenjang]->id;
                
                if ($currentJenjangId != $newJenjangId) {
                    $changes[] = [
                        'id' => $sekolah->id,
                        'nama' => $sekolah->nama,
                        'old_jenjang_id' => $currentJenjangId,
                        'new_jenjang_id' => $newJenjangId,
                        'detected_jenjang' => $detectedJenjang,
                    ];

                    if (!$isDryRun) {
                        $sekolah->jenjang_pendidikan_id = $newJenjangId;
                        $sekolah->save();
                    }
                    
                    $stats['updated']++;
                } else {
                    $stats['unchanged']++;
                }
            } else {
                $stats['unknown']++;
                $this->warn("Could not detect jenjang for: {$sekolah->nama}");
            }
        }

        // Display results
        $this->newLine();
        $this->info('=== Summary ===');
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $stats['updated']],
                ['Unchanged', $stats['unchanged']],
                ['Unknown', $stats['unknown']],
            ]
        );

        if (!empty($changes)) {
            $this->newLine();
            $this->info('=== Changes Made ===');
            
            // Group by detected jenjang
            $groupedChanges = collect($changes)->groupBy('detected_jenjang');
            
            foreach ($groupedChanges as $jenjang => $items) {
                $this->info("→ {$jenjang}: {$items->count()} schools");
            }

            if ($this->option('verbose')) {
                $this->newLine();
                $this->table(
                    ['ID', 'Nama', 'Old Jenjang ID', 'New Jenjang ID', 'Detected'],
                    collect($changes)->take(20)->map(function ($change) {
                        return [
                            $change['id'],
                            substr($change['nama'], 0, 40),
                            $change['old_jenjang_id'],
                            $change['new_jenjang_id'],
                            $change['detected_jenjang'],
                        ];
                    })->toArray()
                );
                
                if (count($changes) > 20) {
                    $this->info('... and ' . (count($changes) - 20) . ' more');
                }
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('DRY-RUN mode: No changes were saved to database');
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->newLine();
            $this->info('✓ Successfully updated jenjang_pendidikan_id for all sekolah!');
        }

        return 0;
    }
}
