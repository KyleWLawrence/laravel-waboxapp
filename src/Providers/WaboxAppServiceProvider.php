<?php

namespace KyleWLawrence\WaboxApp\Providers;

use Illuminate\Support\ServiceProvider;
use KyleWLawrence\WaboxApp\Services\WaboxAppService;
use KyleWLawrence\WaboxApp\Services\NullService;

class WaboxAppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider and merge config.
     *
     * @return void
     */
    public function register()
    {
        $packageName = 'waboxapp-laravel';
        $configPath = __DIR__.'/../../config/waboxapp-laravel.php';

        $this->mergeConfigFrom(
            $configPath, $packageName
        );

        $this->publishes([
            $configPath => config_path(sprintf('%s.php', $packageName)),
        ]);
    }

    /**
     * Bind service to 'WaboxApp' for use with Facade.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('WaboxApp', function () {
            $driver = config('waboxapp-laravel.driver', 'api');
            if (is_null($driver) || $driver === 'log') {
                return new NullService($driver === 'log');
            }

            return new WaboxAppService;
        });
    }
}
