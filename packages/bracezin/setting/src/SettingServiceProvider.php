<?php

namespace Setting;

use Illuminate\Support\ServiceProvider;
use Schema;
use Setting\Models\Setting;
use Setting\Observers\SettingObserver;

class SettingServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'setting');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Setting::observe(SettingObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisables();
        $this->mergeConfigFrom(__DIR__.'/../config/settingConfig.php', 'settingConfig');
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
        return [Setting\Helpers\Setting::class];
    }
}
