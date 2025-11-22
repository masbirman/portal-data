<?php

namespace App\Filament\Resources\SiklusAsesmens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiklusAsesmensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun Asesmen')
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(null)
            ->recordActions([
                ViewAction::make()
                    ->modalWidth('sm')
                    ->modalHeading('Detail Tahun Asesmen'),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data Siklus Asesmen?')
                    ->modalDescription('PERHATIAN: Menghapus siklus asesmen akan menghapus SEMUA data pelaksanaan asesmen untuk tahun ini. Tindakan ini tidak dapat dibatalkan!')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Siklus Asesmen?')
                        ->modalDescription('PERHATIAN: Menghapus siklus asesmen akan menghapus SEMUA data pelaksanaan asesmen untuk tahun ini. Tindakan ini tidak dapat dibatalkan!')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ]);
    }
}
