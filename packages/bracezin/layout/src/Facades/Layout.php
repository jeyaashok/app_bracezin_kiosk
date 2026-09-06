<?php

namespace Layout\Facades;

use Illuminate\Support\Facades\Facade;

class Layout extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Layout\Helpers\Layout::class;
	}
}
