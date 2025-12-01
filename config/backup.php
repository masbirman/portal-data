<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Drive Folder ID
    |--------------------------------------------------------------------------
    |
    | The folder ID where backups will be stored in Google Drive.
    |
    */
    'google_drive_folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Credentials Path
    |--------------------------------------------------------------------------
    |
    | Path to the Google service account credentials JSON file.
    |
    */
    'google_credentials_path' => env('GOOGLE_CREDENTIALS_PATH', storage_path('app/google-credentials.json')),
];
