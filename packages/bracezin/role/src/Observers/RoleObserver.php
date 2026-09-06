<?php

namespace Role\Observers;

use Role\Models\Role;

class RoleObserver
{
    /**
     * Handle the Role "creating" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function creating(Role $role) {
        $role->flushCache(['role']);
    }

    /**
     * Handle the Role "updating" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function saving(Role $role) {
        $role->flushCache(['role']);
    }

    /**
     * Handle the Role "deleting" event.
     *
     * @param  \App\Role  $role
     * @return void
     */
    public function deleting(Role $role) {
        $role->flushCache(['role']);
    }


}
