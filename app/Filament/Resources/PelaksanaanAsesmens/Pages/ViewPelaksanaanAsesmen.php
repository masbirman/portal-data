<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Pages;

use App\Filament\Resources\PelaksanaanAsesmens\PelaksanaanAsesmenResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPelaksanaanAsesmen extends ViewRecord
{
    protected static string $resource = PelaksanaanAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
