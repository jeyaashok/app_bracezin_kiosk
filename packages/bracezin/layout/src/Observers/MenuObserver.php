<?php

namespace Layout\Observers;

use Layout\Models\Menu;

class MenuObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $menu
     * @return void
     */
    public function creating(Menu $menu) { }

    public function created(Menu $menu) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $menu
     * @return void
     */
    public function saving(Menu $menu) { }

    public function saved(Menu $menu) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $menu
     * @return void
     */
    public function deleting(Menu $menu) { }

    public function delete(Menu $menu) { }

}
