<?php

namespace User\Facades;

use Illuminate\Support\Facades\Facade;

class UserFcd extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \User\Helpers\UserFcd::class;
	}
}
