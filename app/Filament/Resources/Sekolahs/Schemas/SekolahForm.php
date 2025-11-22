<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_sekolah')
                    ->label('Kode Sekolah')
                    ->required(),
                TextInput::make('nama')
                    ->label('Nama Sekolah')
                    ->required(),
                Select::make('status_sekolah')
                    ->label('Status Sekolah')
                    ->options([
                        'Negeri' => 'Negeri',
                        'Swasta' => 'Swasta',
                    ])
                    ->nullable(),
                TagsInput::make('tahun')
                    ->label('Tahun')
                    ->placeholder('Tambah tahun')
                    ->separator(','),
                TextInput::make('jenjang_pendidikan_id')
                    ->label('Kode Jenjang Pendidikan')
                    ->required()
                    ->numeric(),
                TextInput::make('wilayah_id')
                    ->label('Kode Wilayah')
                    ->required()
                    ->numeric(),
            ]);
    }
}
