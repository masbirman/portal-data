<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected Client $client;
    protected Drive $driveService;
    protected string $folderId;

    public function __construct()
    {
        $this->folderId = config('backup.google_drive_folder_id');
        $this->initClient();
    }

    protected function initClient(): void
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));
        $this->client->addScope(Drive::DRIVE);
        $this->driveService = new Drive($this->client);
    }

    public function upload(string $filePath, string $fileName): ?string
    {
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
                'supportsAllDrives' => true,
            ]);

            return $file->id;
        } catch (\Exception $e) {
            \Log::error('Google Drive upload failed: ' . $e->getMessage());
            return null;
        }
    }


    public function download(string $fileId): ?string
    {
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

    public function getFile(string $fileId): ?array
    {
        try {
            $file = $this->driveService->files->get($fileId, [
                'fields' => 'id, name, size, createdTime',
            ]);

            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'size' => $file->getSize(),
                'created_at' => $file->getCreatedTime(),
            ];
        } catch (\Exception $e) {
            \Log::error('Google Drive get file failed: ' . $e->getMessage());
            return null;
        }
    }
}
