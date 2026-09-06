<?php

namespace Directory;

use Illuminate\Support\ServiceProvider;
use Schema;

class DirectoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'directory');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisables();
        $this->mergeConfigFrom(__DIR__.'/../config/directoryConfig.php', 'directoryConfig');
    }

    /**
     * Register Publishables.
     *
     * @return void
     */
    public function registerPublisables()
    {
        $basePath = dirname(__DIR__);
        $arrayPublishables = [
            'config' => [
                "$basePath/config" => config_path(),
            ],
            'images' => [
                "$basePath/publishable/storage/app" => storage_path('app/private/'),
            ],
        ];

        foreach ($arrayPublishables as $group => $path) {
            $this->publishes($path, $group);
        }
    }

    /**
     * services this provider provides
     *
     * @return array
     */
    public function provides()
    {
        return [Directory\Helpers\Media::class];
    }
}
