<?php

namespace Role\Traits;

trait RoleTrait
{
    // /********** RelationShip Functions *********///

    public function getUserPermissionsAttribute()
    {
        $userPermissions = [];
        if ($this && $this->tableName === 'users') {
            $allPermissions = ($this && $this->id) ? $this->getAllPermissions()->pluck('name') : [];
            $rolePermissions = ($this && $this->id) ? $this->getPermissionsViaRoles()->pluck('name') : [];
            $directPermissions = ($this && $this->id) ? $this->getDirectPermissions()->pluck('name') : [];
            $userPermissions['allPermissions'] = $allPermissions;
            $userPermissions['rolePermissions'] = $rolePermissions;
            $userPermissions['directPermissions'] = $directPermissions;
        }

        return $this->attributes['userPermissions'] = $userPermissions;
    }
}
