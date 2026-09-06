<?php

namespace Security;

use Illuminate\Support\ServiceProvider;
use Schema;
use Security\Models\Qr;
use Security\Observers\QrObserver;
use Security\Models\Otp;
use Security\Observers\OtpObserver;

class SecurityServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadViewsFrom(__DIR__ . '/./../resources/views', 'security');
		$this->loadMigrationsFrom(__DIR__ . '/./../database/migrations');

		Qr::observe(QrObserver::class);
		Otp::observe(OtpObserver::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerPublisables();
		$this->mergeConfigFrom(__DIR__.'/../config/securityConfig.php', 'securityConfig');
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
		return [Security\Helpers\Qr::class,
				Security\Helpers\Otp::class];
	}

}
