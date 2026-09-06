<?php

namespace Setting\Observers;

use Setting\Models\Setting;

class SettingObserver	
{
	/**
	 * Handle the Role "creating" event.
	 * @param  \App\Role  $setting
	 * @return void
	 */
	public function creating(Setting $setting) { }

	public function created(Setting $setting) { }

	/**
	 * Handle the Role "updating" event.
	 * @param  \App\Role  $setting
	 * @return void
	 */
	public function saving(Setting $setting) { }

	public function saved(Setting $setting) { }

	/**
	 * Handle the Role "deleting" event.
	 * @param  \App\Role  $setting
	 * @return void
	 */
	public function deleting(Setting $setting) { }

	public function delete(Setting $setting) { }

}
