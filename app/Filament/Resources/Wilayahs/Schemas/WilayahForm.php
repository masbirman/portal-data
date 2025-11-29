<?php

namespace App\Filament\Resources\Wilayahs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WilayahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Kota/Kabupaten')
                    ->required(),
                TextInput::make('urutan')
                    ->label('Urutan')
                    ->numeric()
                    ->required()
                    ->default(999)
                    ->helperText('Urutan tampilan di tabel (semakin kecil semakin atas)'),
                \Filament\Forms\Components\FileUpload::make('logo')
                    ->label('Logo Kota/Kabupaten')
                    ->image()
                    ->disk('public')
                    ->directory('logos')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('200')
                    ->imageResizeTargetHeight('200')
                    ->nullable(),
            ]);
    }
}
