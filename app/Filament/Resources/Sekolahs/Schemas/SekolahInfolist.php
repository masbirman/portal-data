<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SekolahInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('nama')
                            ->label('Nama Sekolah')
                            ->badge()
                            ->color('success')
                            ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),
                    ]),

                \Filament\Infolists\Components\Section::make('Identitas')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('npsn')
                            ->label('NPSN')
                            ->badge()
                            ->color(fn(?string $state): string => $state ? 'success' : 'danger')
                            ->placeholder('Belum ada'),
                        \Filament\Infolists\Components\TextEntry::make('kode_sekolah')
                            ->label('Kode Sekolah')
                            ->badge()
                            ->color('gray'),
                        \Filament\Infolists\Components\TextEntry::make('jenjangPendidikan.nama')
                            ->label('Jenjang Pendidikan')
                            ->badge()
                            ->color('info'),
                        \Filament\Infolists\Components\TextEntry::make('wilayah.nama')
                            ->label('Kota/Kabupaten')
                            ->badge()
                            ->color('warning'),
                        \Filament\Infolists\Components\TextEntry::make('status_sekolah')
                            ->label('Status Sekolah')
                            ->badge()
                            ->color(fn(string $state = null): string => match ($state) {
                                'Negeri' => 'success',
                                'Swasta' => 'warning',
                                default => 'gray',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('tahun')
                            ->label('Tahun Pelaksanaan')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),

                \Filament\Infolists\Components\Section::make('Alamat & Lokasi')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->placeholder('Belum ada alamat'),
                        \Filament\Infolists\Components\TextEntry::make('latitude')
                            ->label('Latitude')
                            ->placeholder('-'),
                        \Filament\Infolists\Components\TextEntry::make('longitude')
                            ->label('Longitude')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
