<?php

namespace App\Filament\Resources\DownloadRequests\Pages;

use App\Filament\Resources\DownloadRequests\DownloadRequestResource;
use App\Models\DownloadRequest;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDownloadRequests extends ListRecords
{
    protected static string $resource = DownloadRequestResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-rectangle-stack')
                ->badge(DownloadRequest::count()),

            'pending' => Tab::make('Pending')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(DownloadRequest::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'approved' => Tab::make('Approved')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(DownloadRequest::where('status', 'approved')->count())
                ->badgeColor('success'),

            'rejected' => Tab::make('Rejected')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(DownloadRequest::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        // Default ke pending jika ada, kalau tidak ke semua
        return DownloadRequest::where('status', 'pending')->exists() ? 'pending' : 'semua';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->resetTable()),
            Actions\CreateAction::make(),
        ];
    }
}
