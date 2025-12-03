<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->label('User')
                    ->searchable()
                    ->default('-'),
                TextColumn::make('user.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin_wilayah' => 'warning',
                        'user_sekolah' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin_wilayah' => 'Admin Wilayah',
                        'user_sekolah' => 'User Sekolah',
                        default => '-',
                    }),
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
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                    ]),
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin_wilayah' => 'Admin Wilayah',
                        'user_sekolah' => 'User Sekolah',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, string $role): Builder => $query->whereHas(
                                'user',
                                fn (Builder $query) => $query->where('role', $role)
                            )
                        );
                    }),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
