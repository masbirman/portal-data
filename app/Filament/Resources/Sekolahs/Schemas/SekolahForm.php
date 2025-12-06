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
                \Filament\Forms\Components\Section::make('Identitas Sekolah')
                    ->schema([
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20)
                            ->placeholder('Nomor Pokok Sekolah Nasional')
                            ->helperText('NPSN dari Kemendikdasmen'),
                        TextInput::make('kode_sekolah')
                            ->label('Kode Sekolah')
                            ->required(),
                        TextInput::make('nama')
                            ->label('Nama Sekolah')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('jenjang_pendidikan_id')
                            ->label('Jenjang Pendidikan')
                            ->relationship('jenjangPendidikan', 'nama')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('wilayah_id')
                            ->label('Kota/Kabupaten')
                            ->relationship('wilayah', 'nama')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status_sekolah')
                            ->label('Status Sekolah')
                            ->options([
                                'Negeri' => 'Negeri',
                                'Swasta' => 'Swasta',
                            ])
                            ->nullable(),
                        TagsInput::make('tahun')
                            ->label('Tahun Pelaksanaan')
                            ->placeholder('Tambah tahun')
                            ->separator(','),
                    ])
                    ->columns(2),

                \Filament\Forms\Components\Section::make('Alamat & Lokasi')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Alamat lengkap sekolah'),
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('-1.234567'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('119.123456'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
