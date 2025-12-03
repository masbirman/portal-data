<?php

namespace App\Filament\SekolahPanel\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Dashboard Sekolah';

    public function getHeading(): string
    {
        $user = auth()->user();
        $sekolahName = $user->sekolah?->nama ?? 'Sekolah Tidak Ditemukan';

        return "Dashboard {$sekolahName}";
    }

    public function getSubheading(): ?string
    {
        $user = auth()->user();
        $sekolah = $user->sekolah;

        if (!$sekolah) {
            return 'Sekolah belum terhubung dengan akun Anda';
        }

        $jenjang = $sekolah->jenjangPendidikan?->nama ?? '-';
        $wilayah = $sekolah->wilayah?->nama ?? '-';

        return "Jenjang: {$jenjang} | Wilayah: {$wilayah}";
    }
}
