<?php

namespace Layout\Observers;

use Layout\Models\SubMenu;

class SubMenuObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $subMenu
     * @return void
     */
    public function creating(SubMenu $subMenu) { }

    public function created(SubMenu $subMenu) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $subMenu
     * @return void
     */
    public function saving(SubMenu $subMenu) { }

    public function saved(SubMenu $subMenu) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $subMenu
     * @return void
     */
    public function deleting(SubMenu $subMenu) { }

    public function delete(SubMenu $subMenu) { }

}
