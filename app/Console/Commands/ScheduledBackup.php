<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Settings\BackupSettings;
use Illuminate\Console\Command;

class ScheduledBackup extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Run scheduled database backup to Google Drive';

    public function handle(): int
    {
        $settings = app(BackupSettings::class);

        if (!$settings->scheduled_backup_enabled) {
            $this->info('Scheduled backup is disabled.');
            return self::SUCCESS;
        }

        $this->info('Starting backup...');

        try {
            $service = new BackupService();
            $result = $service->createBackup();

            if ($result['success']) {
                $this->info("Backup successful: {$result['filename']}");
                return self::SUCCESS;
            } else {
                $this->error("Backup failed: {$result['message']}");
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
