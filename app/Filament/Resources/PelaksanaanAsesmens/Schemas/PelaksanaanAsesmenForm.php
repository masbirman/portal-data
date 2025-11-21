<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PelaksanaanAsesmenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('siklus_asesmen_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sekolah_id')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah_peserta')
                    ->label('Jumlah Peserta')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('wilayah_id')
                    ->required()
                    ->numeric(),
                Select::make('status_pelaksanaan')
                    ->options(['Mandiri' => 'Mandiri', 'Menumpang' => 'Menumpang'])
                    ->required(),
                Select::make('moda_pelaksanaan')
                    ->options(['Online' => 'Online', 'Semi Online' => 'Semi online'])
                    ->required(),
                TextInput::make('partisipasi_literasi')
                    ->required()
                    ->numeric(),
                TextInput::make('partisipasi_numerasi')
                    ->required()
                    ->numeric(),
                TextInput::make('tempat_pelaksanaan')
                    ->required(),
                TextInput::make('nama_penanggung_jawab')
                    ->required(),
                TextInput::make('nama_proktor')
                    ->required(),
            ]);
    }
}
