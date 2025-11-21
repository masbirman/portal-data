<?php

namespace App\Filament\Resources\JenjangPendidikans\Pages;

use App\Filament\Resources\JenjangPendidikans\JenjangPendidikanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJenjangPendidikans extends ListRecords
{
    protected static string $resource = JenjangPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
