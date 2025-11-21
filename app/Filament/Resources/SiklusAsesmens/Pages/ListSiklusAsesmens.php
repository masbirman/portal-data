<?php

namespace App\Filament\Resources\SiklusAsesmens\Pages;

use App\Filament\Resources\SiklusAsesmens\SiklusAsesmenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiklusAsesmens extends ListRecords
{
    protected static string $resource = SiklusAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
