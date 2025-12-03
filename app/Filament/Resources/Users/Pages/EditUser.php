<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (): void {
                    ActivityLog::log(
                        'delete',
                        "Menghapus user: {$this->record->name} ({$this->record->email})",
                        $this->record
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLog::log(
            'update',
            "Mengupdate user: {$this->record->name} ({$this->record->email})",
            $this->record
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
