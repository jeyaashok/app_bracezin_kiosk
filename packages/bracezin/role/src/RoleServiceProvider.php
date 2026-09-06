<?php

namespace Role;

use Illuminate\Support\ServiceProvider;
use Schema;
use Role\Models\Role;
use Role\Observers\RoleObserver;
use Role\Models\Permission;
use Role\Observers\PermissionObserver;

class RoleServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'role');
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		// Role::observe(RoleObserver::class);
		// Permission::observe(PermissionObserver::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerPublisables();
		$this->mergeConfigFrom(__DIR__ . '/../config/roleConfig.php', 'roleConfig');
	}

	/**
	 * Register Publishables.
	 *
	 * @return void
	 */
	public function registerPublisables() {
		$basePath = dirname(__DIR__);
		$arrayPublishables = [
			'config' => [
				"$basePath/config" => config_path(),
			]
		];

		foreach ($arrayPublishables as $group => $path) {
			$this->publishes($path, $group);
		}
	}

	public function provides() {
		return [Role\Helpers\Role::class];
	}

}
