<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Skip if setting already exists
        if (!\DB::table('settings')->where('name', 'maintenance_image')->where('group', 'general')->exists()) {
            $this->migrator->add('general.maintenance_image', null);
        }
    }
};
