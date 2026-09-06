<?php

namespace Role\Repositories;

// use Spatie\Permission\Models\Permission;
use App\Repositories\MainRepository;
use Arr;
use Role\Models\Permission;
use Role\Models\Role;

class RoleRepository extends MainRepository
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function index($input = null)
    {
        $count = 100;
        $items = $this->model->SearchFilter($input)
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->IdFilterOn($input, 'id')
            ->StringFilterOn($input, 'guard_name')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

        return $items;
    }

    public function all($input = null)
    {
        $with = @$input['with'] ?: null;
        $items = Role::when($with, function ($e) use ($with) {
            return $e->with($with);
        })
            ->get();

        return $items;
    }

    public function findById($id, $input = null)
    {
        $with = @$input['with'] ?: null;
        $item = Role::when(($with && $with === 'permissions'), function ($e) use ($with) {
            return $e->with($with);
        })
            ->find($id);
        if ($item && $item->id && $with && $with === 'permissionNames') {
            $item['permissionNames'] = $item->getPermissionNames()->toArray();
        }

        return $item;
    }

    public function mapPermissionByRole($input)
    {
        $roleId = Arr::has($input, 'role_id') ? $input['role_id'] : null;
        $permissionName = Arr::has($input, 'permission_name') ? $input['permission_name'] : null;
        $permissionId = Arr::has($input, 'permission_id') ? $input['permission_id'] : null;
        if (! $permissionName && $permissionId) {
            $permission = Permission::find($permissionId)->name;
        } else {
            $permission = $permissionName ?: null;
        }
        $state = (Arr::has($input, 'state') && ($input['state'] === true || $input['state'] === 'true')) ? true : false;

        if ($roleId && $permission) {
            $role = $this->findById($roleId);
            if ($state) {
                $role->givePermissionTo($permission);
            } else {
                $role->revokePermissionTo($permission);
            }
        }

        return $this->findById($roleId, ['with' => 'permissions']);
    }

    public function assignPermissionToRole($input)
    {
        $roleId = Arr::has($input, 'role_id') ? $input['role_id'] : null;
        $permissionId = Arr::has($input, 'permission_id') ? $input['permission_id'] : null;
        if ($roleId && $permissionId) {
            $role = $this->findById($roleId);
            $permission = Permission::find($permissionId);
            if ($role && $permission) {
                $role->givePermissionTo($permission);
            }
        }

        return $this->findById($roleId, ['with' => 'permissions']);
    }

    public function removePermissionFromRole($input)
    {
        $roleId = Arr::has($input, 'role_id') ? $input['role_id'] : null;
        $permissionId = Arr::has($input, 'permission_id') ? $input['permission_id'] : null;
        if ($roleId && $permissionId) {
            $role = $this->findById($roleId);
            $permission = Permission::find($permissionId);
            if ($role && $permission) {
                $role->revokePermissionTo($permission);
            }
        }

        return $this->findById($roleId, ['with' => 'permissions']);
    }
}
