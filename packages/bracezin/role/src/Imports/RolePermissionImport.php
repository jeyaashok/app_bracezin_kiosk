<?php

namespace Role\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Role\Models\Permission;
use Role\Models\Role;

class RolePermissionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $permissions)
    {
        $all_group_permissions = Permission::all()->groupBy('guard_name');
        foreach ($permissions as $role_permissions) {
            $roleName = trim($role_permissions['role'] ?? '');
            if ($roleName === '') {
                continue;
            }

            $guard = trim($role_permissions['guard'] ?? '');
            $role_model = Role::query()
                ->where('name', '=', $roleName)
                ->when($guard !== '', function ($query) use ($guard) {
                    return $query->where('guard_name', '=', $guard);
                })
                ->first();

            if (! $role_model) {
                continue;
            }

            if ($guard === '' || ! $all_group_permissions->has($guard)) {
                $guard = $role_model->guard_name;
            }

            $permissionValue = trim($role_permissions['permission'] ?? '');
            if ($permissionValue === 'ALL_PERMISSIONS' || $permissionValue === 'ALL_ADMIN') {
                foreach ($all_group_permissions->get($guard, collect()) as $all_permission) {
                    $role_model->givePermissionTo($all_permission->name);
                }
            } else {
                $permissionLists = explode('|', $permissionValue);
                foreach ($permissionLists as $list) {
                    $permissionName = trim($list);
                    if ($permissionName === '') {
                        continue;
                    }

                    $permission = Permission::query()
                        ->where('name', $permissionName)
                        ->where('guard_name', $guard)
                        ->first();

                    if ($permission) {
                        $role_model->givePermissionTo($permission->name);
                    }
                }
            }
        }
    }
}
