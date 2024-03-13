<?php

namespace RichanFongdasen\Glide\Tests\Features;

use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Glide\Tests\TestCase;

class GenerateUrlFromCommandTest extends TestCase
{
    static protected array $filterOptions = [
        null,
        'greyscale',
        'sepia',
    ];

    static protected array $fitOptions = [
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

    static protected array $flipOptions = [
        null,
        'v',
        'h',
        'both',
    ];

    static protected array $formatOptions = [
        null,
        'jpg',
        'png',
        'gif',
        'webp',
        'avif',
    ];

    static protected array $orientationOptions = [
        null,
        'auto',
        '0',
        '90',
        '180',
        '270',
    ];

    #[Test]
    public function it_throws_exception_when_there_are_no_asset_path_specified(): void
    {
        $this->expectException(\ErrorException::class);

        $this->artisan('glide:url', ['asset_path' => '']);
    }

    #[Test]
    public function it_throws_exception_when_the_given_value_exceed_the_integer_number_range(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->artisan('glide:url', ['asset_path' => 'images/dummy.jpg'])
            ->expectsQuestion('Please enter the hostname of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('Please enter the URL scheme of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('(Optional) If you wish to resize the image, please enter the image width: [100 - 2000] ', 2800);
    }

    #[Test]
    public function it_throws_exception_when_the_given_value_exceed_the_float_number_range(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->artisan('glide:url', ['asset_path' => 'images/dummy.jpg'])
            ->expectsQuestion('Please enter the hostname of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('Please enter the URL scheme of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('(Optional) If you wish to resize the image, please enter the image width: [100 - 2000] ', 800)
            ->expectsQuestion('(Optional) If you wish to resize the image, please enter the image height: [100 - 2000] ', 600)
            ->expectsChoice('(Optional) Please specify how the image is fitted to its target dimensions: ', 'max', self::$fitOptions)
            ->expectsQuestion('(Optional) If you wish to sharpen the image, please enter the sharpen value: [1 - 100] ', 15)
            ->expectsQuestion('(Optional) Please specify the image device pixel ratio: [1 - 8] ', 2)
            ->expectsChoice('(Optional) Please specify the image orientation: ', '180', self::$orientationOptions)
            ->expectsQuestion('(Optional) If you wish to adjust the image brightness, please specify the adjustment value: [-100 - 100] ', 10)
            ->expectsQuestion('(Optional) If you wish to adjust the image contrast, please specify the adjustment value: [-100 - 100] ', 20)
            ->expectsQuestion('(Optional) If you wish to adjust the image gamma, please specify the adjustment value: [0.1 - 9.9] ', 12.8);
    }

    #[Test]
    public function it_can_generate_the_glide_url_correctly(): void
    {
        $this->artisan('glide:url', ['asset_path' => 'images/dummy.jpg'])
            ->expectsQuestion('Please enter the hostname of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('Please enter the URL scheme of your Glide server. Leave it blank to use the default value.', null)
            ->expectsQuestion('(Optional) If you wish to resize the image, please enter the image width: [100 - 2000] ', 800)
            ->expectsQuestion('(Optional) If you wish to resize the image, please enter the image height: [100 - 2000] ', 600)
            ->expectsChoice('(Optional) Please specify how the image is fitted to its target dimensions: ', 'max', self::$fitOptions)
            ->expectsQuestion('(Optional) If you wish to sharpen the image, please enter the sharpen value: [1 - 100] ', 15)
            ->expectsQuestion('(Optional) Please specify the image device pixel ratio: [1 - 8] ', 2)
            ->expectsChoice('(Optional) Please specify the image orientation: ', '180', self::$orientationOptions)
            ->expectsQuestion('(Optional) If you wish to adjust the image brightness, please specify the adjustment value: [-100 - 100] ', 10)
            ->expectsQuestion('(Optional) If you wish to adjust the image contrast, please specify the adjustment value: [-100 - 100] ', 20)
            ->expectsQuestion('(Optional) If you wish to adjust the image gamma, please specify the adjustment value: [0.1 - 9.9] ', 2)
            ->expectsChoice('(Optional) Please specify the image flip direction: ', 'h', self::$flipOptions)
            ->expectsChoice('(Optional) Please specify the image filter type: ', 'greyscale', self::$filterOptions)
            ->expectsChoice('(Optional) Please specify the image format: ', 'webp', self::$formatOptions)
            ->expectsQuestion('(Optional) Please specify the image quality: [1 - 100] ', 100)
            ->assertExitCode(0)
            ->assertSuccessful()
            ->expectsOutput('http://localhost/assets/images/dummy.jpg?bri=10&con=20&dpr=2&filt=greyscale&fit=max&flip=h&fm=webp&gam=2&h=600&or=180&q=100&s=b14e228d15e30213bb2b2f2acbd6a982&sharp=15&w=800');
    }
}
