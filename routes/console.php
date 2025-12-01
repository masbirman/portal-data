<?php

use App\Settings\BackupSettings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Backup - Daily
Schedule::command('backup:run')->dailyAt('02:00')->when(function () {
    try {
        $settings = app(BackupSettings::class);
        return $settings->scheduled_backup_enabled && $settings->backup_schedule === 'daily';
    } catch (\Exception $e) {
        return false;
    }
});

// Scheduled Backup - Weekly
Schedule::command('backup:run')->weeklyOn(1, '02:00')->when(function () {
    try {
        $settings = app(BackupSettings::class);
        return $settings->scheduled_backup_enabled && $settings->backup_schedule === 'weekly';
    } catch (\Exception $e) {
        return false;
    }
});
