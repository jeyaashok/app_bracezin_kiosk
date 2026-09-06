<?php

namespace User;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Schema;
use User\Helpers\UserFcd;
use User\Observers\UserObserver;

class UserServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'user');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisables();
        $this->mergeConfigFrom(__DIR__.'/../config/userConfig.php', 'userConfig');
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
            'userModel' => [
                "$basePath/publishable/UserModel" => app_path('Models'),
            ],
        ];

        foreach ($arrayPublishables as $group => $path) {
            $this->publishes($path, $group);
        }
    }

    public function provides()
    {
        return [UserFcd::class];
    }
}
