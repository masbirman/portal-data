<?php

namespace App\Filament\Resources\DownloadRequests\Pages;

use App\Filament\Resources\DownloadRequests\DownloadRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDownloadRequests extends ListRecords
{
    protected static string $resource = DownloadRequestResource::class;

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
