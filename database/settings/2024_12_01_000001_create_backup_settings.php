<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('backup.scheduled_backup_enabled', false);
        $this->migrator->add('backup.backup_schedule', 'daily');
        $this->migrator->add('backup.backup_time', '02:00');
        $this->migrator->add('backup.encryption_enabled', false);
        $this->migrator->add('backup.encryption_password', '');
        $this->migrator->add('backup.auto_delete_enabled', true);
        $this->migrator->add('backup.retention_days', 30);
        $this->migrator->add('backup.telegram_notification_enabled', true);
    }
};
