<?php

namespace App\Filament\Resources\DownloadRequests\Schemas;

use App\Models\JenjangPendidikan;
use App\Models\Wilayah;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DownloadRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pemohon')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('instansi')
                    ->label('Instansi')
                    ->required()
                    ->maxLength(255),
                Textarea::make('tujuan_penggunaan')
                    ->label('Tujuan Penggunaan')
                    ->required()
                    ->rows(3),
                Select::make('data_type')
                    ->label('Jenis Data')
                    ->options([
                        'asesmen_nasional' => 'Asesmen Nasional (ANBK)',
                        'survei_lingkungan_belajar' => 'Survei Lingkungan Belajar',
                        'tes_kemampuan_akademik' => 'Tes Kemampuan Akademik (TKA)',
                    ])
                    ->required(),
                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($year = $currentYear; $year >= 2023; $year--) {
                            $years[$year] = $year;
                        }
                        return $years;
                    })
                    ->required(),
                Select::make('wilayah_id')
                    ->label('Wilayah')
                    ->options(function () {
                        return [0 => 'Semua Wilayah'] + Wilayah::pluck('nama', 'id')->toArray();
                    })
                    ->searchable()
                    ->required(),
                Select::make('jenjang_pendidikan_id')
                    ->label('Jenjang Pendidikan')
                    ->options(function () {
                        return [0 => 'Semua Jenjang'] + JenjangPendidikan::pluck('nama', 'id')->toArray();
                    })
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn ($context) => $context === 'create'),
                Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->rows(3)
                    ->visible(fn ($context) => $context === 'edit'),
                DateTimePicker::make('approved_at')
                    ->label('Tanggal Disetujui')
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),
                DateTimePicker::make('downloaded_at')
                    ->label('Tanggal Diunduh')
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),
            ]);
    }
}
