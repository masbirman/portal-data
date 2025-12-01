<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use App\Settings\BackupSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class BackupManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cloud-arrow-up';
    protected static ?string $navigationLabel = 'Backup & Restore';
    protected static ?string $title = 'Backup & Restore Database';
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 100;
    protected string $view = 'filament.pages.backup-manager';

    public array $backups = [];
    public bool $isLoading = false;
    public bool $isGoogleConnected = false;

    // Settings form
    public bool $scheduled_backup_enabled = false;
    public string $backup_schedule = 'daily';
    public string $backup_time = '02:00';
    public bool $encryption_enabled = false;
    public string $encryption_password = '';
    public bool $auto_delete_enabled = true;
    public int $retention_days = 30;
    public bool $telegram_notification_enabled = true;

    public function mount(): void
    {
        $this->loadSettings();
        $this->checkGoogleConnection();
        if ($this->isGoogleConnected) {
            $this->loadBackups();
        }
    }

    protected function checkGoogleConnection(): void
    {
        $googleDrive = new \App\Services\GoogleDriveService();
        $this->isGoogleConnected = $googleDrive->isAuthenticated();
    }

    public function connectGoogle(): void
    {
        $googleDrive = new \App\Services\GoogleDriveService();
        $this->redirect($googleDrive->getAuthUrl());
    }

    public function disconnectGoogle(): void
    {
        $googleDrive = new \App\Services\GoogleDriveService();
        $googleDrive->disconnect();
        $this->isGoogleConnected = false;
        $this->backups = [];

        Notification::make()
            ->title('Google Drive terputus')
            ->success()
            ->send();
    }

    protected function loadSettings(): void
    {
        $settings = app(BackupSettings::class);
        $this->scheduled_backup_enabled = $settings->scheduled_backup_enabled;
        $this->backup_schedule = $settings->backup_schedule;
        $this->backup_time = $settings->backup_time;
        $this->encryption_enabled = $settings->encryption_enabled;
        $this->encryption_password = $settings->encryption_password;
        $this->auto_delete_enabled = $settings->auto_delete_enabled;
        $this->retention_days = $settings->retention_days;
        $this->telegram_notification_enabled = $settings->telegram_notification_enabled;
    }

    public function loadBackups(): void
    {
        try {
            $service = new BackupService();
            $this->backups = $service->listBackups();
        } catch (\Exception $e) {
            $this->backups = [];
            Notification::make()
                ->title('Gagal memuat daftar backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function settingsForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Backup Otomatis')
                    ->schema([
                        Toggle::make('scheduled_backup_enabled')
                            ->label('Aktifkan Backup Terjadwal')
                            ->live(),
                        Select::make('backup_schedule')
                            ->label('Jadwal')
                            ->options([
                                'daily' => 'Setiap Hari',
                                'weekly' => 'Setiap Minggu',
                            ])
                            ->visible(fn () => $this->scheduled_backup_enabled),
                        TextInput::make('backup_time')
                            ->label('Waktu Backup')
                            ->type('time')
                            ->visible(fn () => $this->scheduled_backup_enabled),
                    ])->columns(3),

                Section::make('Keamanan')
                    ->schema([
                        Toggle::make('encryption_enabled')
                            ->label('Enkripsi File Backup')
                            ->live(),
                        TextInput::make('encryption_password')
                            ->label('Password Enkripsi')
                            ->password()
                            ->revealable()
                            ->visible(fn () => $this->encryption_enabled),
                    ])->columns(2),

                Section::make('Retention')
                    ->schema([
                        Toggle::make('auto_delete_enabled')
                            ->label('Auto-Delete Backup Lama')
                            ->live(),
                        TextInput::make('retention_days')
                            ->label('Simpan Backup Selama (hari)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->visible(fn () => $this->auto_delete_enabled),
                    ])->columns(2),

                Section::make('Notifikasi')
                    ->schema([
                        Toggle::make('telegram_notification_enabled')
                            ->label('Kirim Notifikasi Telegram'),
                    ]),
            ])
            ->statePath('data');
    }


    public function saveSettings(): void
    {
        $settings = app(BackupSettings::class);
        $settings->scheduled_backup_enabled = $this->scheduled_backup_enabled;
        $settings->backup_schedule = $this->backup_schedule;
        $settings->backup_time = $this->backup_time;
        $settings->encryption_enabled = $this->encryption_enabled;
        $settings->encryption_password = $this->encryption_password;
        $settings->auto_delete_enabled = $this->auto_delete_enabled;
        $settings->retention_days = $this->retention_days;
        $settings->telegram_notification_enabled = $this->telegram_notification_enabled;
        $settings->save();

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }

    public function createBackup(): void
    {
        $this->isLoading = true;

        try {
            $service = new BackupService();
            $result = $service->createBackup();

            if ($result['success']) {
                Notification::make()
                    ->title('Backup berhasil')
                    ->body("File: {$result['filename']}")
                    ->success()
                    ->send();

                $this->loadBackups();
            } else {
                Notification::make()
                    ->title('Backup gagal')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->isLoading = false;
    }

    public function restoreBackup(string $fileId, string $fileName): void
    {
        $password = $this->encryption_enabled ? $this->encryption_password : null;

        try {
            $service = new BackupService();
            $result = $service->restore($fileId, $password);

            if ($result['success']) {
                Notification::make()
                    ->title('Restore berhasil')
                    ->body('Database berhasil di-restore')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Restore gagal')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Restore gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $fileId): void
    {
        try {
            $service = new BackupService();
            if ($service->deleteBackup($fileId)) {
                Notification::make()
                    ->title('Backup berhasil dihapus')
                    ->success()
                    ->send();

                $this->loadBackups();
            } else {
                Notification::make()
                    ->title('Gagal menghapus backup')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menghapus backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $fileId, string $fileName): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $service = new BackupService();
        $path = (new \App\Services\GoogleDriveService())->download($fileId);

        if (!$path) {
            Notification::make()
                ->title('Download gagal')
                ->danger()
                ->send();
            return response()->noContent();
        }

        return response()->streamDownload(function () use ($path) {
            echo file_get_contents($path);
            unlink($path);
        }, $fileName);
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
