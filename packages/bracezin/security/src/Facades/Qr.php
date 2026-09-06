<?php

namespace Security\Facades;

use Illuminate\Support\Facades\Facade;

class Qr extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Security\Helpers\Qr::class;
	}
}
