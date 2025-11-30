<?php

namespace App\Filament\Resources\DownloadRequests\Pages;

use App\Filament\Resources\DownloadRequests\DownloadRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDownloadRequest extends EditRecord
{
    protected static string $resource = DownloadRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
