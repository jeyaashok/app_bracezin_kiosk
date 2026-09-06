<?php

namespace Remark;

use Illuminate\Support\ServiceProvider;
use Schema;
use Remark\Models\Comment;
use Remark\Models\Feedback;
use Remark\Observers\CommentObserver;
use Remark\Observers\FeedbackObserver;

class RemarkServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadViewsFrom(__DIR__ . '/./../resources/views', 'remark');
		$this->loadMigrationsFrom(__DIR__ . '/./../database/migrations');

		Comment::observe(CommentObserver::class);
		Feedback::observe(FeedbackObserver::class);
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerPublisables();
		$this->mergeConfigFrom(__DIR__.'/../config/remarkConfig.php', 'remarkConfig');
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
		return [Remark\Helpers\Remark::class];
	}

}
