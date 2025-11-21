<?php

namespace App\Filament\Resources\JenjangPendidikans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JenjangPendidikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
            ]);
    }
}
