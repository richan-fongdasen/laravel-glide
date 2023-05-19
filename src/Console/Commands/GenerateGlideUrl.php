<?php

namespace RichanFongdasen\Glide\Console\Commands;

use ErrorException;
use Illuminate\Console\Command;
use InvalidArgumentException;
use RichanFongdasen\Glide\Facade\Glide;

class GenerateGlideUrl extends Command
{
    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Generate a Glide URL for your asset file.';

    /**
     * The collection of image file extensions.
     *
     * @var array<int, string>
     */
    static protected array $imageExtensions = [
        'avif',
        'gif',
        'jpeg',
        'jpg',
        'pjpg',
        'png',
        'webp',
    ];

    /**
     * The hostname of your Glide server.
     *
     * @var string
     */
    protected string $hostname = '';

    /**
     * The Glide parameters that will be used when generating the url.
     *
     * @var array<string, string>
     */
    protected array $params = [];

    /**
     * The URL scheme.
     *
     * @var string
     */
    protected string $scheme = '';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'glide:url {asset_path}';

    /**
     * Ask for the Glide server hostname.
     *
     * @return void
     */
    private function askForHostname(): void
    {
        $this->hostname = trim((string) $this->ask('Please enter the hostname of your Glide server. Leave it blank to use the default value.'));

        if ($this->hostname === '') {
            $this->hostname = config('glide.server_hostname');
        }

        config(['glide.server_hostname' => $this->hostname]);
    }

    /**
     * Ask for the URL scheme.
     *
     * @return void
     */
    private function askForScheme(): void
    {
        $this->scheme = trim((string) $this->ask('Please enter the URL scheme of your Glide server. Leave it blank to use the default value.'));

        if ($this->scheme === '') {
            $this->scheme = config('glide.url_scheme');
        }

        config(['glide.url_scheme' => $this->scheme]);
    }

    /**
     * Get the asset path.
     *
     * @return string
     */
    private function getAssetPath(): string
    {
        return $this->argument('asset_path');
    }

    /**
     * Get the image brightness.
     *
     * @return void
     */
    private function getImageBrightness(): void
    {
        $brightness = $this->ask('(Optional) If you wish to adjust the image brightness, please specify the adjustment value: [-100 - 100] ');

        if ($brightness !== null) {
            $this->validateNumberRange((int) $brightness, -100, 100);
            $this->params['bri'] = (string) $brightness;
        }
    }

    /**
     * Get the image contrast.
     *
     * @return void
     */
    private function getImageContrast(): void
    {
        $contrast = $this->ask('(Optional) If you wish to adjust the image contrast, please specify the adjustment value: [-100 - 100] ');

        if ($contrast !== null) {
            $this->validateNumberRange((int) $contrast, -100, 100);
            $this->params['con'] = (string) $contrast;
        }
    }

    /**
     * Get the image device pixel ratio.
     *
     * @return void
     */
    private function getImageDevicePixelRatio(): void
    {
        $dpr = $this->ask('(Optional) Please specify the image device pixel ratio: [1 - 8] ');

        if ($dpr !== null) {
            $this->validateNumberRange((int) $dpr, 1, 8);
            $this->params['dpr'] = (string) $dpr;
        }
    }

    /**
     * Get the image filter type.
     *
     * @return void
     */
    private function getImageFilter(): void
    {
        $options = [
            null,
            'greyscale',
            'sepia',
        ];

        $filter = $this->choice('(Optional) Please specify the image filter type: ', $options);

        if (is_string($filter) && $filter !== '') {
            $this->params['filt'] = $filter;
        }
    }

    /**
     * Get the specification of how the image is fitted to its target dimensions.
     *
     * @return void
     */
    private function getImageFit(): void
    {
        $options = [
            null,
            'contain',
            'max',
            'fill',
            'fill-max',
            'stretch',
            'crop',
            'crop-top-left',
            'crop-top',
            'crop-top-right',
            'crop-left',
            'crop-center',
            'crop-right',
            'crop-bottom-left',
            'crop-bottom',
            'crop-bottom-right'
        ];

        $fit = $this->choice('(Optional) Please specify how the image is fitted to its target dimensions: ', $options);

        if (is_string($fit) && $fit !== '') {
            $this->params['fit'] = $fit;
        }
    }

    /**
     * Get the image flip direction.
     *
     * @return void
     */
    private function getImageFlip(): void
    {
        $options = [
            null,
            'v',
            'h',
            'both',
        ];

        $flip = $this->choice('(Optional) Please specify the image flip direction: ', $options);

        if (is_string($flip) && $flip !== '') {
            $this->params['flip'] = $flip;
        }
    }

    /**
     * Get the image encoding / format.
     *
     * @return void
     */
    private function getImageFormat(): void
    {
        $options = [
            null,
            'jpg',
            'png',
            'gif',
            'webp',
            'avif',
        ];

        $format = $this->choice('(Optional) Please specify the image format: ', $options);

        if (is_string($format) && $format !== '') {
            $this->params['fm'] = $format;
        }
    }

    /**
     * Get the image gamma.
     *
     * @return void
     */
    private function getImageGamma(): void
    {
        $gamma = $this->ask('(Optional) If you wish to adjust the image gamma, please specify the adjustment value: [0.1 - 9.9] ');

        if ($gamma !== null) {
            $this->validateFloatRange((float) $gamma, 0.1, 9.9);
            $this->params['gam'] = (string) $gamma;
        }
    }

    /**
     * Get the image height.
     *
     * @return void
     */
    private function getImageHeight(): void
    {
        $height = $this->ask('(Optional) If you wish to resize the image, please enter the image height: [100 - 2000] ');

        if ($height !== null) {
            $this->validateNumberRange((int) $height, 100, 1000);
            $this->params['h'] = (string) $height;
        }
    }

    /**
     * Get the image orientation.
     *
     * @return void
     */
    private function getImageOrientation(): void
    {
        $options = [
            null,
            'auto',
            '0',
            '90',
            '180',
            '270',
        ];

        $orientation = $this->choice('(Optional) Please specify the image orientation: ', $options);

        if (is_string($orientation) && $orientation !== '') {
            $this->params['or'] = $orientation;
        }
    }

    /**
     * Get the image quality.
     *
     * @return void
     */
    private function getImageQuality(): void
    {
        $quality = $this->ask('(Optional) Please specify the image quality: [1 - 100] ');

        if ($quality !== null) {
            $this->validateNumberRange((int) $quality, 1, 100);
            $this->params['q'] = (string) $quality;
        }
    }

    /**
     * Get the image sharpen value.
     *
     * @return void
     */
    private function getImageSharpen(): void
    {
        $sharpen = $this->ask('(Optional) If you wish to sharpen the image, please enter the sharpen value: [1 - 100] ');

        if ($sharpen !== null) {
            $this->validateNumberRange((int) $sharpen, 1, 100);
            $this->params['sharp'] = (string) $sharpen;
        }
    }

    /**
     * Get the image width.
     *
     * @return void
     */
    private function getImageWidth(): void
    {
        $width = $this->ask('(Optional) If you wish to resize the image, please enter the image width: [100 - 2000] ');

        if ($width !== null) {
            $this->validateNumberRange((int) $width, 100, 1000);
            $this->params['w'] = (string) $width;
        }
    }
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $path = $this->getAssetPath();
        if ($path === '') {
            throw new ErrorException('Please specify the asset path.');
        }
        $extension = explode('?', data_get(pathinfo($path), 'extension'))[0];

        $this->askForHostname();
        $this->askForScheme();

        if (in_array($extension, self::$imageExtensions, true)) {
            // Crop & resize parameters
            $this->getImageWidth();
            $this->getImageHeight();
            $this->getImageFit();

            // Other image manipulation
            $this->getImageSharpen();
            $this->getImageDevicePixelRatio();
            $this->getImageOrientation();
            $this->getImageBrightness();
            $this->getImageContrast();
            $this->getImageGamma();
            $this->getImageFlip();
            $this->getImageFilter();

            // Final image information
            $this->getImageFormat();

            if (in_array(data_get($this->params, 'fm'), ['jpg', 'webp', 'avif'], true)) {
                $this->getImageQuality();
            }
        }

        $this->info(Glide::url($path, $this->params));

        return 0;
    }

    /**
     * Confirm if a float value is between the minimum and maximum value allowed.
     *
     * @param float $number
     * @param float $min
     * @param float $max
     * @return void
     */
    private function validateFloatRange(float $number, float $min, float $max): void
    {
        if (($number < $min) || ($number > $max)) {
            throw new InvalidArgumentException(sprintf('Please enter a value between %f and %f.', $min, $max));
        }
    }

    /**
     * Confirm if a number is between the minimum and maximum value allowed.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     * @return void
     */
    private function validateNumberRange(int $number, int $min, int $max): void
    {
        if (($number < $min) || ($number > $max)) {
            throw new InvalidArgumentException(sprintf('Please enter a value between %d and %d.', $min, $max));
        }
    }
}