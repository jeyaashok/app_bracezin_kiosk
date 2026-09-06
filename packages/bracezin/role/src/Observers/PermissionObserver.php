<?php

namespace Role\Observers;

use Role\Models\Permission;

class PermissionObserver
{
    /**
     * Handle the Permission "creating" event.
     *
     * @param  \App\Permission  $permission
     * @return void
     */
    public function creating(Permission $permission) {
        $permission->flushCache(['permission']);
        $file_path = storage_path('app/public/json/permissions.json');
        unlink($file_path);
    }

    /**
     * Handle the Permission "updating" event.
     *
     * @param  \App\Permission  $permission
     * @return void
     */
    public function saving(Permission $permission) {
        $permission->flushCache(['permission']);
        $file_path = storage_path('app/public/json/permissions.json');
        unlink($file_path);
    }

    /**
     * Handle the Permission "deleting" event.
     *
     * @param  \App\Permission  $permission
     * @return void
     */
    public function deleting(Permission $permission) {
        $permission->flushCache(['permission']);
        $file_path = storage_path('app/public/json/permissions.json');
        unlink($file_path);
    }


}
