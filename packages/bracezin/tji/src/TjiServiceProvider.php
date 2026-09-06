<?php

namespace Tji;

use Illuminate\Support\ServiceProvider;
use Schema;

class TjiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        setlocale(LC_MONETARY, 'en_IN.UTF-8');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->mergeConfigFrom(__DIR__.'/config/tjiConfig.php', 'tjiConfig');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisables();
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
                "$basePath/src/config" => config_path(),
            ],
            'mainModel' => [
                "$basePath/publishable/Models" => app_path('Models'),
            ],
            'mainController' => [
                "$basePath/publishable/Controllers" => app_path('Http/Controllers'),
            ],
            'mainRepository' => [
                "$basePath/publishable/Repositories" => app_path('Repositories'),
            ],
            'middleware' => [
                "$basePath/publishable/Middleware" => app_path('Http/Middleware'),
            ],
            'exceptions' => [
                "$basePath/publishable/Exceptions" => app_path('Http/Exceptions'),
            ],
        ];

        foreach ($arrayPublishables as $group => $path) {
            $this->publishes($path, $group);
        }
    }

    public function provides()
    {
        return [Tji\Helpers\Tji::class,
            Tji\Helpers\MainHelper::class,
            Tji\Helpers\Constant::class,
            Tji\Facades\Jobs::class];
    }
}
