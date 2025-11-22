<?php

namespace App\Filament\Resources\JenjangPendidikans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JenjangPendidikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode Jenjang')
                    ->searchable(),
                TextColumn::make('nama')
                    ->label('Nama Jenjang')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data Jenjang Pendidikan?')
                    ->modalDescription('PERHATIAN: Menghapus jenjang akan menghapus SEMUA data sekolah dan pelaksanaan asesmen yang terkait. Tindakan ini tidak dapat dibatalkan!')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Jenjang Pendidikan?')
                        ->modalDescription('PERHATIAN: Menghapus jenjang akan menghapus SEMUA data sekolah dan pelaksanaan asesmen yang terkait. Tindakan ini tidak dapat dibatalkan!')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ]);
    }
}
