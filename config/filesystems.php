<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | The default filesystem disk that should be used by the framework. 
    | Make sure the default is set to 'local' unless you're using an external disk.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Configure as many filesystem disks as necessary. 
    | By default, local and public disks are set to their corresponding paths inside the container.
    |
    */

    'disks' => [

        // Local disk (correct path for Docker)
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),  // This should work correctly in Docker
            'throw' => false,
        ],

        // Public disk (correct path for Docker)
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),  // Inside Docker, this should point to /var/www/storage/app/public
            'url' => env('APP_URL').'/storage',  // Ensure APP_URL in .env is correct (e.g., http://localhost:8081)
            'visibility' => 'public',
            'throw' => false,
        ],

        // S3 configuration (if using S3, ensure .env variables are correctly set)
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Map the public 'storage' folder to the 'storage/app/public' directory.
    | This is necessary for serving files from the storage folder.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),  // Docker should respect this mapping
    ],

];
