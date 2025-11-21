<?php

namespace App\Filament\Resources\SiklusAsesmens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiklusAsesmenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun')
                    ->required()
                    ->numeric(),
                TextInput::make('nama')
                    ->required(),
            ]);
    }
}
