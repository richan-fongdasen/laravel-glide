<?php

return [
    'asset_url_prefix' => '/assets',
    'default_format'   => 'webp',
    'disks'            => [
        'cache'  => env('GLIDE_CACHE_DISK', 'local'),
        'source' => env('GLIDE_SOURCE_DISK', 'public'),
    ],
    'driver'           => env('GLIDE_DRIVER', 'imagick'),
    'max_image_size'   => 2048*2048,
    'server'           => true,
    'server_hostname'  => env('GLIDE_SERVER_HOSTNAME', 'localhost:8000'),
    'sign_key'         => env('GLIDE_SIGN_KEY'),
    'url_scheme'       => env('GLIDE_URL_SCHEME', 'http'),
];
