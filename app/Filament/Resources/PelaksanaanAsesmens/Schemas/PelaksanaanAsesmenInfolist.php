<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PelaksanaanAsesmenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sekolah.nama')
                    ->label('Nama Sekolah')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-3xl font-bold px-6 py-3 mb-4',
                    ])
                    ->columnSpanFull(),
                TextEntry::make('jumlah_peserta')
                    ->label('Jumlah Peserta')
                    ->alignCenter()
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->columnSpanFull(),
                TextEntry::make('siklusAsesmen.tahun')
                    ->label('Tahun')
                    ->alignCenter()
                    ->color('info')
                    ->badge(),
                TextEntry::make('sekolah.jenjangPendidikan.nama')
                    ->label('Jenjang Pendidikan')
                    ->alignCenter()
                    ->color('warning')
                    ->badge(),
                TextEntry::make('wilayah.nama')
                    ->label('Kota/Kabupaten')
                    ->alignCenter()
                    ->color('gray')
                    ->badge(),
                TextEntry::make('tempat_pelaksanaan')
                    ->label('Tempat Pelaksanaan')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
                TextEntry::make('status_pelaksanaan')
                    ->label('Status Pelaksanaan')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mandiri' => 'success',
                        'Menumpang' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('moda_pelaksanaan')
                    ->label('Moda Pelaksanaan')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Online' => 'info',
                        'Semi Online' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('partisipasi_literasi')
                    ->label('Partisipasi Literasi')
                    ->alignCenter()
                    ->numeric()
                    ->suffix('%')
                    ->badge()
                    ->color('primary'),
                TextEntry::make('partisipasi_numerasi')
                    ->label('Partisipasi Numerasi')
                    ->alignCenter()
                    ->numeric()
                    ->suffix('%')
                    ->badge()
                    ->color('primary'),
                TextEntry::make('nama_penanggung_jawab')
                    ->label('Nama Penanggung Jawab')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                TextEntry::make('nama_proktor')
                    ->label('Nama Proktor')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
            ])
            ->columns(2);
    }
}
