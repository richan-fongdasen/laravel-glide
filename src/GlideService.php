<?php

namespace RichanFongdasen\Glide;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use RichanFongdasen\Glide\Exceptions\FilesystemException;
use RichanFongdasen\Glide\Exceptions\ReadStreamErrorException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * RichanFongdasen\Glide\GlideService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GlideService
{
    /**
     * Collection of mime types.
     *
     * @var array<string, string>
     */
    static protected array $mimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
    ];

    /**
     * Custom disk creators.
     *
     * @var array<string, ?Closure>
     */
    protected array $diskCreators = [];

    /**
     * References of the created filesystem disks.
     *
     * @var Collection<string, Filesystem>
     */
    protected Collection $disks;

    /**
     * The Glide Service constructor.
     */
    public function __construct()
    {
        $this->disks = new Collection();
    }

    /**
     * Get the Filesystem disk to retrieve the asset from.
     *
     * @param string $diskName
     * @return Filesystem
     */
    public function getDisk(string $diskName): Filesystem
    {
        if ($this->disks->has($diskName)) {
            // @phpstan-ignore-next-line
            return $this->disks->get($diskName);
        }

        $createMethod = data_get($this->diskCreators, $diskName);

        $disk = ($createMethod instanceof Closure) ?
            $createMethod() : Storage::disk(config('glide.disks.'.$diskName));

        if (!($disk instanceof FilesystemAdapter)) {
            throw new FilesystemException('The Glide disk is not a valid implementation of Illuminate\\Filesystem\\FilesystemAdapter.');
        }

        $this->disks->put($diskName, $disk);

        return $disk;
    }

    /**
     * Create Glide Server
     *
     * @return Server
     */
    protected function getServer(): Server
    {
        return ServerFactory::create([
            'source'         => $this->getDisk('source')->getDriver(),
            'cache'          => $this->getDisk('cache')->getDriver(),
            'driver'         => config('glide.driver'),
            'max_image_size' => config('glide.max_image_size'),
        ]);
    }

    /**
     * Process the GlideRequest.
     *
     * @param GlideRequest $request
     * @return StreamedResponse
     */
    protected function processGlideRequest(GlideRequest $request): StreamedResponse
    {
        $server = $this->getServer();
        $path = $request->getAssetPath();
        $params = $request->getGlideParams();

        $headers = array_merge(config('glide.default_headers'), [
            'Content-Type' => data_get(self::$mimeTypes, $request->input('fm'), 'image/jpeg'),
        ]);

        return response()->stream(function () use ($server, $path, $params) {
            $server->outputImage($path, $params);
        }, 200, $headers);
    }

    /**
     * Respond to the given GlideRequest.
     *
     * @param GlideRequest $request
     * @return StreamedResponse
     */
    public function respondToRequest(GlideRequest $request): StreamedResponse
    {
        $sourceDisk = $this->getDisk('source');
        $path = $request->getAssetPath();

        if (!$sourceDisk->exists($path)) {
            abort(404, 'File not found.');
        }

        if (!Str::startsWith((string) $sourceDisk->mimeType($path), 'image/')) {
            if ($request->getGlideParams([]) !== []) {
                abort(403, 'Invalid http query parameters.');
            }

            return $this->streamAssetDirectly($sourceDisk, $path);
        }

        return $this->processGlideRequest($request);
    }

    /**
     * Set custom disk creator.
     *
     * @param string $diskName
     * @param Closure $method
     * @return void
     */
    public function setDisk(string $diskName, Closure $method): void
    {
        $this->diskCreators[$diskName] = $method;
    }

    /**
     * Stream the asset directly for any of non-image assets.
     *
     * @param Filesystem $disk
     * @param string $path
     * @return StreamedResponse
     */
    protected function streamAssetDirectly(Filesystem $disk, string $path): StreamedResponse
    {
        $headers = array_merge(config('glide.default_headers'), [
            'Content-Type' => $disk->mimeType($path),
            'Content-Length' => $disk->size($path),
        ]);

        return response()->stream(function () use ($disk, $path) {
            $resource = $disk->readStream($path);

            if ($resource === null) {
                throw new ReadStreamErrorException('Failed to read file at path '.$path);
            }

            while (!feof($resource)) {
                // @phpstan-ignore-next-line
                echo fread($resource, 1024);
                flush();
            }
        }, 200, $headers);
    }

    /**
     * Get the signed url of the given asset path and parameters.
     *
     * @param string $assetPath
     * @param array $params
     * @return string
     */
    public function url(string $assetPath, array $params = []): string
    {
        $prefix = Str::finish(Str::start(config('glide.asset_url_prefix'), '/'), '/');
        $assetPath = Str::of($assetPath)->ltrim('/')->toString();
        $params['s'] = SignatureFactory::create(config('glide.sign_key'))->generateSignature($assetPath, array_merge([
            'hostname' => config('glide.server_hostname'),
        ], $params));

        ksort($params);

        return sprintf(
            '%s://%s%s%s?%s',
            config('glide.url_scheme'),
            config('glide.server_hostname'),
            $prefix,
            $assetPath,
            http_build_query($params)
        );
    }

    /**
     * Validate Glide Request.
     *
     * @param GlideRequest $request
     * @return bool
     */
    public function validateRequest(GlideRequest $request): bool
    {
        try {
            SignatureFactory::create(config('glide.sign_key'))->validateRequest(
                $request->getAssetPath(),
                $request->getSignatureValidationParams()
            );
        } catch (SignatureException $exception) {
            abort(403, $exception->getMessage());
        }

        return true;
    }
}
