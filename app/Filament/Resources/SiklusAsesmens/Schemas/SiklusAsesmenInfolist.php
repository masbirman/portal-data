<?php

namespace App\Filament\Resources\SiklusAsesmens\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiklusAsesmenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('nama')
                    ->hiddenLabel()
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-5xl font-bold px-8 py-4 mb-4',
                    ]),
                \Filament\Infolists\Components\TextEntry::make('tahun')
                    ->hiddenLabel()
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-2xl font-semibold px-6 py-3',
                    ]),
            ])
            ->columns(1);
    }
}
