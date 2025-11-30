<?php

namespace App\Filament\Resources\DownloadRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DownloadRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('instansi')
                    ->label('Instansi')
                    ->searchable(),
                TextColumn::make('data_type')
                    ->label('Jenis Data')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'asesmen_nasional' => 'ANBK',
                        'survei_lingkungan_belajar' => 'SLB',
                        'tes_kemampuan_akademik' => 'TKA',
                        default => $state,
                    })
                    ->badge(),
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('wilayah.nama')
                    ->label('Wilayah')
                    ->sortable(),
                TextColumn::make('jenjangPendidikan.nama')
                    ->label('Jenjang')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal Request')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('downloaded_at')
                    ->label('Diunduh')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'approved';
                        $record->approved_by = auth()->id();
                        $record->approved_at = now();
                        $record->generateDownloadToken();
                        $record->save();

                        // Send email notification
                        \Illuminate\Support\Facades\Mail::to($record->email)
                            ->send(new \App\Mail\DownloadRequestApproved($record));
                        
                        Notification::make()
                            ->title('Request disetujui dan email telah dikirim')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->status = 'rejected';
                        $record->admin_notes = $data['admin_notes'];
                        $record->approved_by = auth()->id();
                        $record->approved_at = now();
                        $record->save();

                        // Send email notification
                        \Illuminate\Support\Facades\Mail::to($record->email)
                            ->send(new \App\Mail\DownloadRequestRejected($record));
                        
                        Notification::make()
                            ->title('Request ditolak dan email telah dikirim')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
