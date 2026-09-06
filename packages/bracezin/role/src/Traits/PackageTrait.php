<?php

namespace Role\Traits;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Auth;
use Log;

/******* Available Index *********
Filter Funtions:-

 ******* Index *********/

trait PackageTrait {

///********** RelationShip Functions *********///
    public function getPermissionArrayAttribute() {
        $model = @$this->user()->first();
        $permissions = ($model && $model->id) ? $model->getAllPermissions()->pluck('name') : null;
        return $this->attributes['permissionArray'] = $permissions;
    }

    public function getDirectPermissionArrayAttribute() {
        $model = @$this->user()->first();
        $directPermissions = ($model && $model->id) ? $model->getDirectPermissions()->pluck('name') : null;
        return $this->attributes['directPermissionArray'] = $directPermissions;
    }

    public function getPermissionIdArrayAttribute() {
        $model = @$this->user()->first();
        $permissionIds = ($model && $model->id) ? $model->getAllPermissions()->pluck('id') : null;
        return $this->attributes['permissionIdArray'] = $permissionIds;
    }


    public function getDirectPermissionIdArrayAttribute() {
        $model = @$this->user()->first();
        $directPermissionIds = ($model && $model->id) ? $model->getDirectPermissions()->pluck('id') : null;
        return $this->attributes['directPermissionIdArray'] = $directPermissionIds;
    }

    public function getRoleArrayAttribute() {
        $model = @$this->user()->first();
        $roles = ($model && $model->id) ? $model->getRoleNames() : null;
        return $this->attributes['roleArray'] = $roles;
    }

    public function getRoleIdArrayAttribute() {
        $model = @$this->user()->first();
        $roleIds = ($model && $model->id) ? $model->roles()->pluck('id') : null;
        return $this->attributes['roleIdArray'] = $roleIds;
    }

}
