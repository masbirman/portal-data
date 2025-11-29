<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $site_active;
    public string $maintenance_message;
    public ?string $maintenance_image;
    public ?string $maintenance_estimated_time;

    public static function group(): string
    {
        return 'general';
    }
}
