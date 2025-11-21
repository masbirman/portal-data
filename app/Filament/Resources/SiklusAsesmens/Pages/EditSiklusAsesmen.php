<?php

namespace App\Filament\Resources\SiklusAsesmens\Pages;

use App\Filament\Resources\SiklusAsesmens\SiklusAsesmenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSiklusAsesmen extends EditRecord
{
    protected static string $resource = SiklusAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
