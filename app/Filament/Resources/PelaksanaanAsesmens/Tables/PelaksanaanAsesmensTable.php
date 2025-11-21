<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PelaksanaanAsesmensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siklusAsesmen.tahun')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('sekolah.jenjangPendidikan.nama')
                    ->label('Jenjang')
                    ->sortable(),
                TextColumn::make('wilayah.nama')
                    ->label('Kota/Kabupaten')
                    ->sortable(),
                TextColumn::make('sekolah.kode_sekolah')
                    ->label('Kode Sekolah')
                    ->sortable(),
                TextColumn::make('sekolah.nama')
                    ->label('Nama Sekolah')
                    ->sortable(),
                TextColumn::make('jumlah_peserta')
                    ->label('Jumlah Peserta')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status_pelaksanaan')
                    ->label('Status Pelaksanaan')
                    ->sortable(),
                TextColumn::make('moda_pelaksanaan')
                    ->label('Moda Pelaksanaan')
                    ->sortable(),
                TextColumn::make('partisipasi_literasi')
                    ->label('Partisipasi Literasi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('partisipasi_numerasi')
                    ->label('Partisipasi Numerasi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tempat_pelaksanaan')
                    ->label('Tempat Pelaksanaan')
                    ->searchable(),
                TextColumn::make('nama_penanggung_jawab')
                    ->label('Nama Penanggung Jawab')
                    ->searchable(),
                TextColumn::make('nama_proktor')
                    ->label('Nama Proktor')
                    ->searchable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('siklus_asesmen_id')
                    ->label('Tahun')
                    ->relationship('siklusAsesmen', 'tahun')
                    ->preload()
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('jenjang_pendidikan')
                    ->label('Jenjang Pendidikan')
                    ->options(function () {
                        return \App\Models\JenjangPendidikan::pluck('nama', 'id')->toArray();
                    })
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? $data['value'] ?? null;

                        if (! empty($values)) {
                            $query->whereHas('sekolah', function ($q) use ($values) {
                                $q->whereIn('jenjang_pendidikan_id', (array) $values);
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
                \Filament\Tables\Filters\SelectFilter::make('status_pelaksanaan')
                    ->label('Status Pelaksanaan')
                    ->options([
                        'Mandiri' => 'Mandiri',
                        'Menumpang' => 'Menumpang',
                    ])
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('moda_pelaksanaan')
                    ->label('Moda Pelaksanaan')
                    ->options([
                        'Online' => 'Online',
                        'Semi Online' => 'Semi Online',
                    ])
                    ->multiple(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('md')
                        ->modalHeading('Detail Pelaksanaan Asesmen'),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->searchDebounce('500ms')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->persistFiltersInSession()
            ->filtersFormColumns(3)
            ->defaultSort('id', 'desc');
    }
}
