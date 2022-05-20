<?php

namespace RichanFongdasen\Glide\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTest;

abstract class TestCase extends BaseTest
{
    /**
     * Define environment setup
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $this->app = $app;

        $app['config']->set('cache.default', 'array');
        $app['config']->set('glide', [
            'asset_url_prefix' => 'assets',
            'default_format'   => 'jpg',
            'disks'            => [
                'cache'  => 'local',
                'source' => 'public',
            ],
            'driver'           => 'imagick',
            'max_image_size'   => 2048*2048,
            'server'           => true,
            'server_hostname'  => 'localhost',
            'sign_key'         => 'c*pGmuHqHBFW5*4Z((x2H:]KSQ:OGgmN8MOC',
            'url_scheme'       => 'http',
        ]);
    }

    /**
     * Define package aliases
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        $this->app = $app;

        return [
            'Glide' => \RichanFongdasen\Glide\Facade\Glide::class,
        ];
    }

    /**
     * Define package service provider
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        $this->app = $app;

        return [
            \RichanFongdasen\Glide\GlideServiceProvider::class,
        ];
    }

    /**
     * Invoke protected / private method of the given object
     *
     * @param mixed $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeMethod(mixed $object,string  $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Get any protected / private property value
     *
     * @param mixed $object
     * @param string $propertyName
     * @return mixed
     * @throws \ReflectionException
     */
    public function getPropertyValue(mixed $object, string $propertyName): mixed
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
