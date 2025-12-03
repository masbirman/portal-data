<?php

namespace App\Filament\WilayahPanel\Resources;

use App\Filament\WilayahPanel\Resources\SekolahResource\Pages;
use App\Models\Sekolah;
use App\Models\Scopes\WilayahJenjangScope;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SekolahResource extends Resource
{
    protected static ?string $model = Sekolah::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Data Sekolah';

    protected static ?string $modelLabel = 'Sekolah';

    protected static ?string $pluralModelLabel = 'Data Sekolah';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        (new WilayahJenjangScope())->apply($query, new Sekolah());
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_sekolah')
                    ->label('Kode Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenjangPendidikan.nama')
                    ->label('Jenjang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('wilayah.nama')
                    ->label('Kota/Kabupaten')
                    ->sortable(),
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
                SelectFilter::make('jenjang_pendidikan_id')
                    ->label('Jenjang')
                    ->options(function () {
                        // Hanya tampilkan jenjang yang ditugaskan ke user
                        $user = auth()->user();
                        if ($user && $user->isAdminWilayah()) {
                            return $user->jenjangs()->pluck('nama', 'jenjang_pendidikan.id')->toArray();
                        }
                        return [];
                    })
                    ->searchable(),
                SelectFilter::make('status_sekolah')
                    ->label('Status')
                    ->options([
                        'Negeri' => 'Negeri',
                        'Swasta' => 'Swasta',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSekolahs::route('/'),
            'view' => Pages\ViewSekolah::route('/{record}'),
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
