<?php

namespace App\Filament\Resources\JenjangPendidikans\Pages;

use App\Filament\Resources\JenjangPendidikans\JenjangPendidikanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJenjangPendidikan extends ViewRecord
{
    protected static string $resource = JenjangPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
