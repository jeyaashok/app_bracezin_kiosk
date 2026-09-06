<?php

namespace Notification;

use Illuminate\Support\ServiceProvider;
use Notification\Models\Notify;
use Notification\Observers\NotifyObserver;
use Schema;

class NotificationServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/./../resources/views', 'notification');
        $this->loadMigrationsFrom(__DIR__.'/./../database/migrations');

        Notify::observe(NotifyObserver::class);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisables();
        $this->mergeConfigFrom(__DIR__.'/../config/notificationConfig.php', 'notificationConfig');
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
            'mail' => [
                "$basePath/publishable/Mail" => app_path('Mail'),
            ],
            'views' => [
                "$basePath/publishable/views/" => resource_path('views'),
            ],
        ];

        foreach ($arrayPublishables as $group => $path) {
            $this->publishes($path, $group);
        }
    }

    public function provides()
    {
        return [Notification\Helpers\Notification::class];
    }
}
