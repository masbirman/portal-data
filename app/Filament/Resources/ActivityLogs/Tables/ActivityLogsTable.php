<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Admin')
                    ->searchable()
                    ->default('-'),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn ($record) => $record->action_badge_color),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Aksi')
                    ->options([
                        'approve' => 'Approve',
                        'reject' => 'Reject',
                        'backup' => 'Backup',
                        'restore' => 'Restore',
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Admin')
                    ->relationship('user', 'name'),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
