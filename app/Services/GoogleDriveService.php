<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    protected Client $client;
    protected ?Drive $driveService = null;
    protected string $folderId;

    public function __construct()
    {
        $this->folderId = config('backup.google_drive_folder_id');
        $this->initClient();
    }

    protected function initClient(): void
    {
        $this->client = new Client();
        $this->client->setClientId(config('backup.google_client_id'));
        $this->client->setClientSecret(config('backup.google_client_secret'));
        $this->client->setRedirectUri(config('backup.google_redirect_uri'));
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Load saved token
        $token = Cache::get('google_drive_token');
        if ($token) {
            $this->client->setAccessToken($token);

            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    Cache::forever('google_drive_token', $newToken);
                }
            }

            $this->driveService = new Drive($this->client);
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->driveService !== null && !$this->client->isAccessTokenExpired();
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate(string $code): bool
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            if (isset($token['error'])) {
                \Log::error('Google OAuth error: ' . $token['error']);
                return false;
            }
            Cache::forever('google_drive_token', $token);
            $this->client->setAccessToken($token);
            $this->driveService = new Drive($this->client);
            return true;
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return false;
        }
    }

    public function disconnect(): void
    {
        Cache::forget('google_drive_token');
        $this->driveService = null;
    }

    public function upload(string $filePath, string $fileName): ?string
    {
        if (!$this->driveService) {
            \Log::error('Google Drive not authenticated');
            return null;
        }

        try {
            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => [$this->folderId],
            ]);

            $content = file_get_contents($filePath);
            $mimeType = str_ends_with($fileName, '.gz') ? 'application/gzip' : 'application/octet-stream';

            $file = $this->driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]);

            return $file->id;
        } catch (\Exception $e) {
            \Log::error('Google Drive upload failed: ' . $e->getMessage());
            return null;
        }
    }

    public function download(string $fileId): ?string
    {
        if (!$this->driveService) {
            return null;
        }

        try {
            $response = $this->driveService->files->get($fileId, ['alt' => 'media']);
            $content = $response->getBody()->getContents();

            $tempPath = storage_path('app/backups/temp_' . time() . '.sql.gz');

            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            file_put_contents($tempPath, $content);

            return $tempPath;
        } catch (\Exception $e) {
            \Log::error('Google Drive download failed: ' . $e->getMessage());
            return null;
        }
    }

    public function delete(string $fileId): bool
    {
        if (!$this->driveService) {
            return false;
        }

        try {
            $this->driveService->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Drive delete failed: ' . $e->getMessage());
            return false;
        }
    }

    public function listFiles(): array
    {
        if (!$this->driveService) {
            return [];
        }

        try {
            $results = $this->driveService->files->listFiles([
                'q' => "'{$this->folderId}' in parents and trashed = false",
                'fields' => 'files(id, name, size, createdTime, modifiedTime)',
                'orderBy' => 'createdTime desc',
            ]);

            return array_map(function ($file) {
                return [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'size' => $file->getSize(),
                    'created_at' => $file->getCreatedTime(),
                    'modified_at' => $file->getModifiedTime(),
                ];
            }, $results->getFiles());
        } catch (\Exception $e) {
            \Log::error('Google Drive list failed: ' . $e->getMessage());
            return [];
        }
    }
}
