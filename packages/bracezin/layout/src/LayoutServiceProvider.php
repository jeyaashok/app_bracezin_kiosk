<?php

namespace Layout;

use Illuminate\Support\ServiceProvider;
use Schema;
use Layout\Models\Menu;
use Layout\Models\MenuGroup;
use Layout\Models\SubMenu;
use Layout\Observers\MenuObserver;
use Layout\Observers\MenuGroupObserver;
use Layout\Observers\SubMenuObserver;

class LayoutServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'layout');
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		Menu::observe(MenuObserver::class);
		MenuGroup::observe(MenuGroupObserver::class);
		SubMenu::observe(SubMenuObserver::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerPublisables();
		$this->mergeConfigFrom(__DIR__ . '/../config/layoutConfig.php', 'layoutConfig');
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
			],
		];

		foreach ($arrayPublishables as $group => $path) {
			$this->publishes($path, $group);
		}
	}
	/**
	 * services this provider provides
	 * @return array
	 */
	public function provides() {
		return [Layout\Helpers\Layout::class];
	}

}
