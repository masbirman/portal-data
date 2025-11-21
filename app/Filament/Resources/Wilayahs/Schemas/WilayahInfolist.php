<?php

namespace App\Filament\Resources\Wilayahs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WilayahInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\ImageEntry::make('logo')
                    ->hiddenLabel()
                    ->disk('public')
                    ->defaultImageUrl(url('/images/default-logo.png'))
                    ->size(120)
                    ->alignCenter()
                    ->extraAttributes(['class' => 'mb-4']),
                \Filament\Infolists\Components\TextEntry::make('nama')
                    ->hiddenLabel()
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-5xl font-bold px-8 py-4',
                    ]),
            ])
            ->columns(1);
    }
}
