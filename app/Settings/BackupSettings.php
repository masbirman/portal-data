<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BackupSettings extends Settings
{
    public bool $scheduled_backup_enabled;
    public string $backup_schedule; // daily, weekly
    public string $backup_time;
    public bool $encryption_enabled;
    public string $encryption_password;
    public bool $auto_delete_enabled;
    public int $retention_days;
    public bool $telegram_notification_enabled;

    public static function group(): string
    {
        return 'backup';
    }
}
