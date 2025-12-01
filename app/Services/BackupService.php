<?php

namespace App\Services;

use App\Settings\BackupSettings;
use Illuminate\Support\Facades\Process;

class BackupService
{
    protected GoogleDriveService $googleDrive;
    protected TelegramService $telegram;

    public function __construct()
    {
        $this->googleDrive = new GoogleDriveService();
        $this->telegram = new TelegramService();
    }

    public function createBackup(): array
    {
        $settings = app(BackupSettings::class);
        $timestamp = now()->format('Ymd_His');
        $env = config('app.env');
        $filename = "backup_{$env}_{$timestamp}.sql";
        $gzFilename = "{$filename}.gz";
        $backupPath = storage_path("app/backups/{$filename}");
        $gzPath = storage_path("app/backups/{$gzFilename}");

        // Ensure backup directory exists
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }

        // Create mysqldump
        $result = $this->mysqldump($backupPath);

        if (!$result['success']) {
            return $result;
        }

        // Compress the backup
        $this->compress($backupPath, $gzPath);

        // Encrypt if enabled
        if ($settings->encryption_enabled && $settings->encryption_password) {
            $encryptedPath = $gzPath . '.enc';
            $this->encrypt($gzPath, $encryptedPath, $settings->encryption_password);
            unlink($gzPath);
            $gzPath = $encryptedPath;
            $gzFilename .= '.enc';
        }

        // Upload to Google Drive
        $fileId = $this->googleDrive->upload($gzPath, $gzFilename);

        // Clean up local file
        unlink($gzPath);
        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        if (!$fileId) {
            $this->sendNotification(false, 'Upload ke Google Drive gagal');
            return ['success' => false, 'message' => 'Upload ke Google Drive gagal'];
        }

        // Send notification
        $this->sendNotification(true, "Backup {$gzFilename} berhasil");

        // Auto-delete old backups
        if ($settings->auto_delete_enabled) {
            $this->deleteOldBackups($settings->retention_days);
        }

        return [
            'success' => true,
            'message' => 'Backup berhasil',
            'file_id' => $fileId,
            'filename' => $gzFilename,
        ];
    }

    protected function mysqldump(string $outputPath): array
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($outputPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            return ['success' => false, 'message' => 'Mysqldump gagal: ' . implode("\n", $output)];
        }

        return ['success' => true];
    }

    protected function compress(string $source, string $destination): void
    {
        $data = file_get_contents($source);
        $gzData = gzencode($data, 9);
        file_put_contents($destination, $gzData);
        unlink($source);
    }

    protected function decompress(string $source, string $destination): void
    {
        $gzData = file_get_contents($source);
        $data = gzdecode($gzData);
        file_put_contents($destination, $data);
    }

    protected function encrypt(string $source, string $destination, string $password): void
    {
        $data = file_get_contents($source);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $password, OPENSSL_RAW_DATA, $iv);
        file_put_contents($destination, $iv . $encrypted);
    }

    protected function decrypt(string $source, string $destination, string $password): bool
    {
        $data = file_get_contents($source);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $password, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            return false;
        }

        file_put_contents($destination, $decrypted);
        return true;
    }


    public function restore(string $fileId, ?string $password = null): array
    {
        // Download from Google Drive
        $downloadedPath = $this->googleDrive->download($fileId);

        if (!$downloadedPath) {
            return ['success' => false, 'message' => 'Download dari Google Drive gagal'];
        }

        $sqlPath = storage_path('app/backups/restore_' . time() . '.sql');

        try {
            // Check if encrypted
            if (str_ends_with($downloadedPath, '.enc') || $this->isEncrypted($downloadedPath)) {
                if (!$password) {
                    unlink($downloadedPath);
                    return ['success' => false, 'message' => 'File terenkripsi, password diperlukan'];
                }

                $decryptedPath = $downloadedPath . '.dec';
                if (!$this->decrypt($downloadedPath, $decryptedPath, $password)) {
                    unlink($downloadedPath);
                    return ['success' => false, 'message' => 'Password salah atau file corrupt'];
                }
                unlink($downloadedPath);
                $downloadedPath = $decryptedPath;
            }

            // Decompress
            $this->decompress($downloadedPath, $sqlPath);
            unlink($downloadedPath);

            // Import to database
            $result = $this->mysqlImport($sqlPath);

            // Clean up
            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }

            if (!$result['success']) {
                $this->sendNotification(false, 'Restore gagal: ' . $result['message']);
                return $result;
            }

            $this->sendNotification(true, 'Restore database berhasil');
            return ['success' => true, 'message' => 'Restore berhasil'];

        } catch (\Exception $e) {
            // Clean up on error
            if (file_exists($downloadedPath)) unlink($downloadedPath);
            if (file_exists($sqlPath)) unlink($sqlPath);

            $this->sendNotification(false, 'Restore gagal: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Restore gagal: ' . $e->getMessage()];
        }
    }

    protected function isEncrypted(string $path): bool
    {
        // Simple check - encrypted files won't start with gzip magic bytes
        $handle = fopen($path, 'rb');
        $header = fread($handle, 2);
        fclose($handle);
        return $header !== "\x1f\x8b"; // gzip magic bytes
    }

    protected function mysqlImport(string $sqlPath): array
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysql -h%s -P%s -u%s -p%s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($sqlPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return ['success' => false, 'message' => 'MySQL import gagal: ' . implode("\n", $output)];
        }

        return ['success' => true];
    }

    public function deleteOldBackups(int $retentionDays): int
    {
        $files = $this->googleDrive->listFiles();
        $deleted = 0;
        $cutoffDate = now()->subDays($retentionDays);

        foreach ($files as $file) {
            $createdAt = \Carbon\Carbon::parse($file['created_at']);
            if ($createdAt->lt($cutoffDate)) {
                if ($this->googleDrive->delete($file['id'])) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    public function listBackups(): array
    {
        return $this->googleDrive->listFiles();
    }

    public function deleteBackup(string $fileId): bool
    {
        return $this->googleDrive->delete($fileId);
    }

    protected function sendNotification(bool $success, string $message): void
    {
        $settings = app(BackupSettings::class);

        if (!$settings->telegram_notification_enabled) {
            return;
        }

        $icon = $success ? '✅' : '❌';
        $status = $success ? 'Sukses' : 'Gagal';

        $telegramMessage = "{$icon} <b>Backup {$status}</b>\n\n"
            . "📝 {$message}\n"
            . "🕐 " . now()->format('d M Y, H:i') . " WITA";

        $this->telegram->sendMessage($telegramMessage);
    }
}
