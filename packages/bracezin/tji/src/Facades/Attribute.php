<?php

namespace Tji\Facades;

use Illuminate\Support\Facades\Facade;

class Attribute extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \Illuminate\Database\Eloquent\Casts\Attribute::class;
	}
}
