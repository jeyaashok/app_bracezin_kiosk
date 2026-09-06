<?php

namespace Tji\Facades;

use Illuminate\Support\Facades\Facade;

class MainHelper extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Tji\Helpers\MainHelper::class;
	}
}
