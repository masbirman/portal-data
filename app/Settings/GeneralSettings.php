<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $site_active;
    public string $maintenance_message;
    public ?string $maintenance_image;

    public static function group(): string
    {
        return 'general';
    }
}
