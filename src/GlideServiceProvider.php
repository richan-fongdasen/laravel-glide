<?php

namespace RichanFongdasen\Glide;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RichanFongdasen\Glide\Console\Commands\GenerateGlideUrl;

class GlideServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/glide.php', 'glide');

        $this->app->scoped(GlideService::class, static function () {
            return new GlideService();
        });

        $this->commands([GenerateGlideUrl::class]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            realpath(dirname(__DIR__).'/config/glide.php') => config_path('glide.php'),
        ], 'config');

        if (config('glide.server') === true) {
            Route::prefix(Str::of(config('glide.asset_url_prefix'))->ltrim('/')->rtrim('/')->toString())
                ->middleware([SubstituteBindings::class])
                ->namespace('RichanFongdasen\\Glide\\Http\\Controllers')
                ->group(static function () {
                    Route::get('{url}', 'GlideController@show')->where('url', '.+');
                });
        }
    }
}
