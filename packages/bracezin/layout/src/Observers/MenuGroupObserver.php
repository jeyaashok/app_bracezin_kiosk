<?php

namespace Layout\Observers;

use Layout\Models\MenuGroup;

class MenuGroupObserver
{
	/**
	 * Handle the Role "creating" event.
	 * @param  \App\Role  $menuGroup
	 * @return void
	 */
	public function creating(MenuGroup $menuGroup) { }

	public function created(MenuGroup $menuGroup) { }

	/**
	 * Handle the Role "updating" event.
	 * @param  \App\Role  $menuGroup
	 * @return void
	 */
	public function saving(MenuGroup $menuGroup) { }

	public function saved(MenuGroup $menuGroup) { }

	/**
	 * Handle the Role "deleting" event.
	 * @param  \App\Role  $menuGroup
	 * @return void
	 */
	public function deleting(MenuGroup $menuGroup) { }

	public function delete(MenuGroup $menuGroup) { }

}
