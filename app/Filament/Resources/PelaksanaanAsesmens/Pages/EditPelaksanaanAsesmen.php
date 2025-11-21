<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Pages;

use App\Filament\Resources\PelaksanaanAsesmens\PelaksanaanAsesmenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPelaksanaanAsesmen extends EditRecord
{
    protected static string $resource = PelaksanaanAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
