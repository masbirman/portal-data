<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Maintenance Mode';
    protected static ?string $title = 'Maintenance Mode';
    protected static ?string $slug = 'maintenance-mode';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.pages.manage-general-settings';

    public bool $site_active = true;
    public string $maintenance_message = '';
    public ?string $maintenance_image = null;
    public ?string $maintenance_estimated_time = null;

    public function mount(): void
    {
        $settings = app(GeneralSettings::class);

        $this->site_active = $settings->site_active;
        $this->maintenance_message = $settings->maintenance_message;
        $this->maintenance_image = $settings->maintenance_image;
        $this->maintenance_estimated_time = $settings->maintenance_estimated_time;
    }

    public function settingsForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status Website')
                    ->description('Kontrol ketersediaan website untuk publik')
                    ->schema([
                        Toggle::make('site_active')
                            ->label('Status Website Aktif')
                            ->helperText('Jika dinonaktifkan, pengunjung akan melihat halaman maintenance')
                            ->default(true),
                    ]),

                Section::make('Pengaturan Maintenance')
                    ->description('Konfigurasi tampilan halaman maintenance')
                    ->schema([
                        Textarea::make('maintenance_message')
                            ->label('Pesan Maintenance')
                            ->rows(3)
                            ->helperText('Pesan yang ditampilkan kepada pengunjung saat maintenance'),

                        TextInput::make('maintenance_estimated_time')
                            ->label('Estimasi Waktu Selesai')
                            ->placeholder('Contoh: 2 jam, 30 menit, dll')
                            ->helperText('Informasi estimasi waktu maintenance selesai (opsional)'),

                        FileUpload::make('maintenance_image')
                            ->label('Gambar Ilustrasi')
                            ->image()
                            ->directory('maintenance')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
                            ->helperText('Upload gambar ilustrasi untuk halaman maintenance (max 2MB, JPG/PNG/SVG)'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->site_active = $this->site_active;
        $settings->maintenance_message = $this->maintenance_message;
        $settings->maintenance_image = $this->maintenance_image;
        $settings->maintenance_estimated_time = $this->maintenance_estimated_time;
        $settings->save();

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
