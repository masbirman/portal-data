<?php

namespace App\Filament\Resources\DownloadRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                TextColumn::make('wilayah_display')
                    ->label('Wilayah')
                    ->state(function ($record) {
                        return $record->wilayah_id ? ($record->wilayah->nama ?? '-') : 'Semua Wilayah';
                    })
                    ->sortable(),
                TextColumn::make('jenjang_display')
                    ->label('Jenjang')
                    ->state(function ($record) {
                        return $record->jenjang_pendidikan_id ? ($record->jenjangPendidikan->nama ?? '-') : 'Semua Jenjang';
                    })
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
                ActionGroup::make([
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
                    Action::make('regenerate_token')
                        ->label('Regenerate Token')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn ($record) => $record->status === 'approved' && !$record->isTokenValid())
                        ->requiresConfirmation()
                        ->modalHeading('Regenerate Download Token')
                        ->modalDescription('Token baru akan digenerate dan email akan dikirim ulang ke user. Lanjutkan?')
                        ->action(function ($record) {
                            $record->generateDownloadToken();
                            $record->downloaded_at = null;
                            $record->save();

                            // Send email notification
                            \Illuminate\Support\Facades\Mail::to($record->email)
                                ->send(new \App\Mail\DownloadRequestApproved($record));

                            Notification::make()
                                ->title('Token berhasil di-regenerate dan email telah dikirim')
                                ->success()
                                ->send();
                        }),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'approved';
                                    $record->approved_by = auth()->id();
                                    $record->approved_at = now();
                                    $record->generateDownloadToken();
                                    $record->save();

                                    // Send email notification
                                    \Illuminate\Support\Facades\Mail::to($record->email)
                                        ->send(new \App\Mail\DownloadRequestApproved($record));

                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("$count request berhasil disetujui dan email telah dikirim")
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->form([
                            \Filament\Forms\Components\Textarea::make('admin_notes')
                                ->label('Alasan Penolakan')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'rejected';
                                    $record->admin_notes = $data['admin_notes'];
                                    $record->approved_by = auth()->id();
                                    $record->approved_at = now();
                                    $record->save();

                                    // Send email notification
                                    \Illuminate\Support\Facades\Mail::to($record->email)
                                        ->send(new \App\Mail\DownloadRequestRejected($record));

                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("$count request berhasil ditolak dan email telah dikirim")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
