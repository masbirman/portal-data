<?php

namespace App\Filament\Widgets;

use App\Models\DownloadRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DownloadRequestStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pending = DownloadRequest::where('status', 'pending')->count();
        $approved = DownloadRequest::where('status', 'approved')->count();
        $rejected = DownloadRequest::where('status', 'rejected')->count();
        $downloaded = DownloadRequest::whereNotNull('downloaded_at')->count();

        return [
            Stat::make('Pending', $pending)
                ->description('Menunggu persetujuan')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Disetujui', $approved)
                ->description('Request disetujui')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Ditolak', $rejected)
                ->description('Request ditolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Diunduh', $downloaded)
                ->description('Sudah didownload')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info'),
        ];
    }
}
