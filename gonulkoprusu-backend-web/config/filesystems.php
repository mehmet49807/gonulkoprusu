<?php

return [

    'default' => env('FILESYSTEM_DISK', 'public'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | FTP Medya Diski — Kullanıcı & Gönderi Resimleri
        |--------------------------------------------------------------------------
        | Dosyalar public_html/uploads altına yüklenir.
        | URL: https://gonulkoprusu.com/uploads/profiles|posts|stories/...
        */
        'ftp_media' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'port' => (int) env('FTP_PORT', 21),
            'root' => env('FTP_MEDIA_ROOT', '/home/gonulkop/domains/gonulkoprusu.com/public_html/uploads'),
            'passive' => env('FTP_PASSIVE', true),
            'ssl' => env('FTP_SSL', false),
            'timeout' => 30,
            'throw' => false,
        ],

        'media_local' => [
            'driver' => 'local',
            'root' => env('MEDIA_LOCAL_ROOT', public_path('uploads')),
            'url' => env('MEDIA_URL', 'https://gonulkoprusu.com/uploads'),
            'visibility' => 'public',
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Medya yükleme sırası (profil, gönderi, hikaye)
    |--------------------------------------------------------------------------
    | Paylaşımlı hostingde PHP doğrudan public_html/uploads'a yazabilmeli.
    | FTP yalnızca yerel disk yazılamazsa yedek olarak kullanılır.
    */
    'media_disks' => array_values(array_filter([
        env('MEDIA_DISK', 'media_local'),
        env('MEDIA_FALLBACK_DISK', 'ftp_media'),
    ])),

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
