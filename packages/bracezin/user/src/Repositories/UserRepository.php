<?php

namespace User\Repositories;

use App\Models\User;
use App\Repositories\MainRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class UserRepository extends MainRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Returns Index all records.
     *
     * @return Collection|static[]
     */
    public function index($input = null)
    {
        $input = $input ?: [];
        $count = 100;
        $items = $this->model
            ->SetAppendByRequest($input)
            ->DateFilter($input)
            ->NullFilterOn($input)
            ->IdFilterOn($input, 'id')
            ->StringFilterOn($input, 'type')
            ->StringFilterOn($input, 'name')
            ->StringFilterOn($input, 'email', false)
            ->StringFilterOn($input, 'username')
            ->StringFilterOn($input, 'mobile')
            ->StringFilterOn($input, 'default_lang')
            ->BooleanFilterOn($input, 'is_sysAdmin')
            ->BooleanFilterOn($input, 'do_change_password')
            ->BooleanFilterOn($input, 'do_reset_password')
            ->BooleanFilterOn($input, 'is_active')
            ->BooleanFilterOn($input, 'is_email_verified')
            ->BooleanFilterOn($input, 'is_sound_notify')
            ->IdFilterOn($input, 'created_by')
            ->IdFilterOn($input, 'updated_by')
            ->OrderByFilter($input)
            ->DeletedFilter($input)
            ->IncludeFilter($input);

        return $items;
    }

    public function mapPermissionByUser(array $input)
    {
        $userId = Arr::get($input, 'user_id');
        $roles = Arr::get($input, 'roles', []);
        $permissions = Arr::get($input, 'permissions', []);

        $user = $this->model->findOrFail($userId);
        if (method_exists($user, 'syncRoles') && is_array($roles) && count($roles) > 0) {
            $user->syncRoles($roles);
        }
        if (method_exists($user, 'syncPermissions') && is_array($permissions) && count($permissions) > 0) {
            $user->syncPermissions($permissions);
        }

        $state = Arr::get($input, 'state', false);
        $roleName = Arr::get($input, 'role_name', null);
        $permissionName = Arr::get($input, 'permission_name', null);
        if ($state && $state === true) {
            if (method_exists($user, 'assignRole') && $roleName) {
                $user->assignRole($roleName);
            }
            if (method_exists($user, 'givePermissionTo') && $permissionName) {
                $user->givePermissionTo($permissionName);
            }
        } else {
            if (method_exists($user, 'removeRole') && $roleName) {
                $user->removeRole($roleName);
            }
            if (method_exists($user, 'revokePermissionTo') && $permissionName) {
                $user->revokePermissionTo($permissionName);
            }
        }

        return $user->fresh();
    }
}
