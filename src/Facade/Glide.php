<?php

namespace RichanFongdasen\Glide\Facade;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\Glide\GlideService;

/**
 * RichanFongdasen\Glide\Facade
 *
 * @method static \Illuminate\Contracts\Filesystem\Filesystem getDisk(string $diskName)
 * @method static \League\Glide\Server getServer()
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse respondToRequest(\RichanFongdasen\Glide\GlideRequest $request)
 * @method static void setDisk(string $diskName, \Closure $method)
 * @method static string url(string $assetPath, array $params = [])
 * @method static bool validateRequest(\RichanFongdasen\Glide\GlideRequest $request)
 */
class Glide extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return GlideService::class;
    }
}