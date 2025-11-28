<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Sistem';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static string $settings = GeneralSettings::class;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Mode Maintenance')
                    ->description('Atur status ketersediaan website untuk publik.')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('site_active')
                            ->label('Status Website Aktif')
                            ->helperText('Jika dimatikan, website akan menampilkan halaman maintenance.')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        \Filament\Forms\Components\Textarea::make('maintenance_message')
                            ->label('Pesan Maintenance')
                            ->helperText('Pesan yang akan ditampilkan saat mode maintenance aktif.')
                            ->rows(3)
                            ->default('Kami sedang melakukan pemeliharaan sistem.')
                            ->required(),

                        \Filament\Forms\Components\FileUpload::make('maintenance_image')
                            ->label('Gambar Ilustrasi')
                            ->helperText('Upload gambar ilustrasi untuk halaman maintenance.')
                            ->image()
                            ->directory('maintenance')
                            ->disk('public')
                            ->visibility('public'),
                    ]),
            ]);
    }
}
