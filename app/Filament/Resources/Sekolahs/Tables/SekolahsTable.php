<?php

namespace App\Filament\Resources\Sekolahs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Sekolah;

class SekolahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenjangPendidikan.nama')
                    ->label('Jenjang Pendidikan')
                    ->sortable(),
                TextColumn::make('wilayah.nama')
                    ->label('Kota/Kabupaten')
                    ->sortable(),
                TextColumn::make('kode_sekolah')
                    ->label('Kode Sekolah')
                    ->searchable(),
                TextColumn::make('nama')
                    ->label('Nama Sekolah')
                    ->searchable(),
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    
                    ->badge()
                    ->separator(',')
                    ->color(fn (string $state): string => match ($state) {
                        '2024' => 'success',
                        '2025' => 'info',
                        '2023' => 'warning',
                        '2022' => 'danger',
                        default => 'primary',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('tahun_filter')
                    ->label('Tahun')
                    ->options(function (): array {
                            $years = Sekolah::query()
                                ->whereNotNull('tahun')
                                ->pluck('tahun')
                                ->flatten()
                                ->unique()
                                ->sortDesc()
                                ->toArray();
                            
                            return array_combine($years, $years); 
                        })
                    ->query(function ($query, array $data) {
                        // \Illuminate\Support\Facades\Log::info('Filter Data:', $data);
                        $values = $data['values'] ?? $data['value'] ?? null;

                        if (! empty($values)) {
                            $query->where(function ($q) use ($values) {
                                foreach ((array) $values as $value) {
                                    $q->orWhereJsonContains('tahun', $value);
                                }
                            });
                        }
                    })
                    ->preload()
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('jenjang_pendidikan_id')
                    ->label('Jenjang Pendidikan')
                    ->relationship('jenjangPendidikan', 'nama')
                    ->preload()
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('wilayah_id')
                    ->label('Kota/Kabupaten')
                    ->relationship('wilayah', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('md')
                        ->modalHeading('Detail Sekolah'),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
