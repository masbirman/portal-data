<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Sistem';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Mode Maintenance')
                    ->description('Atur status ketersediaan website untuk publik.')
                    ->schema([
                        Toggle::make('site_active')
                            ->label('Status Website Aktif')
                            ->helperText('Jika dimatikan, website akan menampilkan halaman maintenance.')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        Textarea::make('maintenance_message')
                            ->label('Pesan Maintenance')
                            ->helperText('Pesan yang akan ditampilkan saat mode maintenance aktif.')
                            ->rows(3)
                            ->default('Kami sedang melakukan pemeliharaan sistem.')
                            ->required(),

                        FileUpload::make('maintenance_image')
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
