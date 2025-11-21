<?php

namespace App\Filament\Resources\SiklusAsesmens\Pages;

use App\Filament\Resources\SiklusAsesmens\SiklusAsesmenResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSiklusAsesmen extends ViewRecord
{
    protected static string $resource = SiklusAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
