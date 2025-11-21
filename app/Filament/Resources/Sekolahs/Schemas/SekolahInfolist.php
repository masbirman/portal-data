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
                \Filament\Infolists\Components\TextEntry::make('nama')
                    ->label('Nama Sekolah')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-4xl font-bold px-8 py-4 mb-6',
                    ]),
                \Filament\Infolists\Components\TextEntry::make('kode_sekolah')
                    ->label('Kode Sekolah')
                    ->alignCenter()
                    ->color('gray')
                    ->badge(), 
                \Filament\Infolists\Components\TextEntry::make('jenjangPendidikan.nama')
                    ->label('Jenjang Pendidikan')
                    ->alignCenter()
                    ->color('info')
                    ->badge(), 
                \Filament\Infolists\Components\TextEntry::make('wilayah.nama')
                    ->label('Kota/Kabupaten')
                    ->alignCenter()
                    ->color('warning')
                    ->badge(),
                \Filament\Infolists\Components\TextEntry::make('tahun')
                    ->label('Tahun Pelaksanaan')
                    ->alignCenter()
                    ->color('success')
                    ->badge()        
            ])
            ->columns(1);
    }
}
