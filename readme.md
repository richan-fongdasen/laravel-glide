# Laravel Glide

> A simple way to enable Glide in your Laravel Application.

## Synopsis

Glide is a wonderfully easy on-demand image manipulation library written in PHP. Its straightforward API is exposed via HTTP, similar to cloud image processing services like Imgix and Cloudinary.
You can find out more about Glide in [the official documentation](https://glide.thephpleague.com/).

## Table of contents

- [Setup](#setup)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## Setup

Install the package via Composer :

```sh
$ composer require richan-fongdasen/laravel-glide
```

## Configuration

You can publish the package configuration using this `php artisan` command

```sh
$ php artisan vendor:publish --provider="RichanFongdasen\Glide\GlideServiceProvider"
```

The artisan command above should make you a copy of package configuration located in `config/glide.php`

```php
return [
    'asset_url_prefix'       => '/assets',
    'default_headers'        => [
        'Cache-Control' => 'max-age=31536000, public',
    ],
    'default_image_format'   => 'webp',
    'disks'                  => [
        'cache'  => env('GLIDE_CACHE_DISK', 'local'),
        'source' => env('GLIDE_SOURCE_DISK', 'public'),
    ],
    'driver'                 => env('GLIDE_DRIVER', 'imagick'),
    'max_image_size'         => 2048*2048,
    'server'                 => true,
    'server_hostname'        => env('GLIDE_SERVER_HOSTNAME', 'localhost:8000'),
    'sign_key'               => env('GLIDE_SIGN_KEY'),
    'url_scheme'             => env('GLIDE_URL_SCHEME', 'http'),
];
```

## Usage

This section is currently under construction.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
