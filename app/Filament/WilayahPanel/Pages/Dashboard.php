<?php

namespace App\Filament\WilayahPanel\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Dashboard Admin Wilayah';

    public function getHeading(): string
    {
        $user = auth()->user();
        $wilayahNames = $user->wilayahs->pluck('nama')->join(', ');
        $jenjangNames = $user->jenjangs->pluck('nama')->join(', ');

        return 'Dashboard Admin Wilayah';
    }

    public function getSubheading(): ?string
    {
        $user = auth()->user();
        $wilayahNames = $user->wilayahs->pluck('nama')->join(', ') ?: 'Belum ada wilayah';
        $jenjangNames = $user->jenjangs->pluck('nama')->join(', ') ?: 'Belum ada jenjang';

        return "Wilayah: {$wilayahNames} | Jenjang: {$jenjangNames}";
    }
}
