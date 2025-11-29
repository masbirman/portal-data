<?php

namespace App\Console\Commands;

use App\Imports\AsesmenImport;
use App\Imports\SekolahImport;
use App\Models\SiklusAsesmen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportAllExcelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all-excel 
                            {--siklus= : Siklus Asesmen ID (default: latest)}
                            {--type=both : Import type: sekolah, asesmen, or both}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Excel files from storage/app/public/imports directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting automated Excel import...');
        $this->newLine();

        // Get Siklus Asesmen
        $siklusId = $this->option('siklus');
        if (!$siklusId) {
            $siklus = SiklusAsesmen::orderBy('tahun', 'desc')->first();
            if (!$siklus) {
                $this->error('❌ No Siklus Asesmen found. Please run seeders first.');
                return 1;
            }
            $siklusId = $siklus->id;
            $this->info("📅 Using latest Siklus Asesmen: {$siklus->nama} (ID: {$siklusId})");
        } else {
            $siklus = SiklusAsesmen::find($siklusId);
            if (!$siklus) {
                $this->error("❌ Siklus Asesmen with ID {$siklusId} not found.");
                return 1;
            }
            $this->info("📅 Using Siklus Asesmen: {$siklus->nama} (ID: {$siklusId})");
        }

        $this->newLine();

        // Get all Excel files
        $importPath = storage_path('app/public/imports');
        if (!File::exists($importPath)) {
            $this->error("❌ Import directory not found: {$importPath}");
            return 1;
        }

        $files = File::files($importPath);
        $excelFiles = array_filter($files, function ($file) {
            return in_array($file->getExtension(), ['xlsx', 'xls']);
        });

        if (empty($excelFiles)) {
            $this->error('❌ No Excel files found in imports directory.');
            return 1;
        }

        $this->info("📁 Found " . count($excelFiles) . " Excel file(s)");
        $this->newLine();

        $type = $this->option('type');
        $successCount = 0;
        $errorCount = 0;

        // Sort files by size (largest first - likely most complete)
        usort($excelFiles, function ($a, $b) {
            return $b->getSize() - $a->getSize();
        });

        foreach ($excelFiles as $index => $file) {
            $fileName = $file->getFilename();
            $fileSize = round($file->getSize() / 1024, 2);
            
            $totalFiles = count($excelFiles);
            $currentFile = $index + 1;
            $this->info("📄 [{$currentFile}/{$totalFiles}] Processing: {$fileName} ({$fileSize} KB)");

            try {
                // Import Sekolah data
                if (in_array($type, ['sekolah', 'both'])) {
                    $this->line("   ↳ Importing Sekolah data...");
                    Excel::import(new SekolahImport($siklusId), $file->getPathname());
                    $this->line("   ✅ Sekolah data imported");
                }

                // Import Pelaksanaan Asesmen data
                if (in_array($type, ['asesmen', 'both'])) {
                    $this->line("   ↳ Importing Pelaksanaan Asesmen data...");
                    Excel::import(new AsesmenImport($siklusId), $file->getPathname());
                    $this->line("   ✅ Pelaksanaan Asesmen data imported");
                }

                $successCount++;
                $this->newLine();

            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $errorCount++;
                $this->newLine();
            }
        }

        // Summary
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════');
        $this->info('📊 IMPORT SUMMARY');
        $this->info('═══════════════════════════════════════════════════');
        $this->info("✅ Successful: {$successCount} file(s)");
        if ($errorCount > 0) {
            $this->error("❌ Failed: {$errorCount} file(s)");
        }
        $this->newLine();

        // Show final counts
        $this->info('📈 Database Statistics:');
        $sekolahCount = \App\Models\Sekolah::count();
        $asesmenCount = \App\Models\PelaksanaanAsesmen::count();
        $this->table(
            ['Table', 'Count'],
            [
                ['Sekolah', $sekolahCount],
                ['Pelaksanaan Asesmen', $asesmenCount],
            ]
        );

        $this->newLine();
        $this->info('🎉 Import completed!');

        return 0;
    }
}
