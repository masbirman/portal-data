<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.maintenance_estimated_time', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.maintenance_estimated_time');
    }
};
