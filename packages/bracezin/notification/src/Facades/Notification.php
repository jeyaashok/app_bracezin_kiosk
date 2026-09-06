<?php

namespace Notification\Facades;

use Illuminate\Support\Facades\Facade;

class Notification extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Notification\Helpers\Notification::class;
	}
}
