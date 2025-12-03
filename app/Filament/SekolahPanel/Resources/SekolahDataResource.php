<?php

namespace App\Filament\SekolahPanel\Resources;

use App\Filament\SekolahPanel\Resources\SekolahDataResource\Pages;
use App\Models\Sekolah;
use App\Models\Scopes\SekolahScope;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class SekolahDataResource extends Resource
{
    protected static ?string $model = Sekolah::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Data Sekolah Saya';

    protected static ?string $modelLabel = 'Data Sekolah';

    protected static ?string $pluralModelLabel = 'Data Sekolah';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->tap(new SekolahScope());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_sekolah')
                    ->label('Kode Sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenjangPendidikan.nama')
                    ->label('Jenjang'),
                Tables\Columns\TextColumn::make('wilayah.nama')
                    ->label('Wilayah'),
                Tables\Columns\TextColumn::make('status_sekolah')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Negeri' => 'success',
                        'Swasta' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSekolahData::route('/'),
            'view' => Pages\ViewSekolahData::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
