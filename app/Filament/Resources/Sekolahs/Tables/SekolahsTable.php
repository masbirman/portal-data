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
                TextColumn::make('status_sekolah')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state = null): string => match ($state) {
                        'Negeri' => 'success',
                        'Swasta' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()

                    ->badge()
                    ->separator(',')
                    ->color(fn(string $state): string => match ($state) {
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
                        // Use SiklusAsesmen as source of truth for years
                        $years = \App\Models\SiklusAsesmen::query()
                            ->pluck('tahun', 'tahun')
                            ->sortDesc()
                            ->toArray();
                        return $years;
                    })
                    ->query(function ($query, array $data) {
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
                    ->options(function () {
                        // Get unique jenjang names
                        return \App\Models\JenjangPendidikan::query()
                            ->distinct()
                            ->pluck('nama', 'nama')
                            ->toArray();
                    })
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? $data['value'] ?? null;

                        if (! empty($values)) {
                            $query->whereHas('jenjangPendidikan', function ($q) use ($values) {
                                $q->whereIn('nama', (array) $values);
                            });
                        }
                    })
                    ->preload()
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('wilayah_id')
                    ->label('Kota/Kabupaten')
                    ->relationship('wilayah', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('status_sekolah')
                    ->label('Status Sekolah')
                    ->options([
                        'Negeri' => 'Negeri',
                        'Swasta' => 'Swasta',
                    ])
                    ->multiple(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('md')
                        ->modalHeading('Detail Sekolah'),
                    EditAction::make(),
                    \Filament\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Sekolah?')
                        ->modalDescription('PERHATIAN: Menghapus sekolah akan menghapus SEMUA data pelaksanaan asesmen yang terkait. Tindakan ini tidak dapat dibatalkan!')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Sekolah?')
                        ->modalDescription('PERHATIAN: Menghapus sekolah akan menghapus SEMUA data pelaksanaan asesmen yang terkait dengan sekolah ini. Tindakan ini tidak dapat dibatalkan!')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ]);
    }
}
