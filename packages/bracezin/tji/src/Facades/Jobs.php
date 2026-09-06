<?php

namespace Tji\Facades;

use Illuminate\Support\Facades\Facade;

class Jobs extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Tji\Helpers\Jobs::class;
	}
}