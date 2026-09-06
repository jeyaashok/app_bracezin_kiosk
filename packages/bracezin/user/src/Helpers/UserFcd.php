<?php

namespace User\Helpers;

use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tji\Helpers\MainHelper;

class UserFcd extends MainHelper
{
    public function getUserModel($input = null)
    {
        return User::get();
    }

    public function getUser($userId)
    {
        return User::find($userId);
    }

    public function GetFormattedData($userId)
    {
        $userId = ($userId) ? $userId : @auth('admin')->user()->id;
        $user = ($userId) ? User::with('detail')->find($userId) : null;

        if ($user && $user->id) {
            $user['roleNames'] = $user->getRoleNames();
            $user['permissionNames'] = $user->getAllPermissions()->pluck('name');
            $user['directPermissionNames'] = $user->getDirectPermissions()->pluck('name');
        }
        $avatar_url = ($user && $user->avatar_url) ? $user->avatar_url : null;
        $user['avatar_url'] = $avatar_url;

        return $user;
    }

    public function storeUser($input, $type)
    {
        $faker = Factory::create();
        $password = (Arr::has($input, 'password')) ? $input['password'] : Config('userConfig.default_user_password', 'secret');
        $input['password'] = $password;
        $input['is_active'] = (Arr::has($input, 'is_active')) ? $input['is_active'] : 1;
        $input['is_email_verified'] = (Arr::has($input, 'is_email_verified')) ? $input['is_email_verified'] : 0;
        $input['is_sysAdmin'] = (Arr::has($input, 'is_sysAdmin')) ? $input['is_sysAdmin'] : 0;
        $input['username'] = (Arr::has($input, 'username')) ? $input['username'] : $input['email'];
        $input['type'] = $type;
        $user = User::create($input);

        $roleName = (Arr::has($input, 'roleName')) ? $input['roleName'] : null;
        if ($roleName) {
            $user->assignRole($roleName);
        } else {
            $user->assignRole($type);
        }

        return $user;
    }

    public function updateUser($input, $userId = null)
    {
        $user = null;
        $type = (Arr::has($input, 'type')) ? $input['type'] : null;
        $resourceId = (Arr::has($input, 'resource_id')) ? $input['resource_id'] : null;
        $resourceType = (Arr::has($input, 'resource_type')) ? $input['resource_type'] : null;

        if ($userId) {
            $user = User::find($userId);
        } else {
            if ($type) {
                $user = User::where('type', '=', $type)
                    ->first();
            }
        }

        if ($user) {
            if (Arr::has($input, 'password')) {
                $input['password'] = $input['password'];
            }
            $input['username'] = (Arr::has($input, 'username')) ? $input['username'] : $user->username;
            $input['is_active'] = (Arr::has($input, 'is_active')) ? $input['is_active'] : $user->is_active;
            $input['is_email_verified'] = (Arr::has($input, 'is_email_verified')) ? $input['is_email_verified'] : $user->is_email_verified;
            $input['is_sysAdmin'] = (Arr::has($input, 'is_sysAdmin')) ? $input['is_sysAdmin'] : $user->is_sysAdmin;
            $updateData = Arr::except($input, ['id']);
            $user->fill($updateData);
            $user->save();
        } else {
            $user = $this->storeUser($input, $type);
        }

        return $user;
    }

    public function changePassword($id, $password)
    {
        $user = User::find($id);
        $user->fill(['password' => $password]);
        $user->save();

    }

    public function getAllSysAdmin()
    {
        $users = User::where('is_sysAdmin', '>', 0)->get();

        return $users;
    }

    public function getAllCompany()
    {
        $users = User::whereHas('roles', function ($q) {
            return $q->where('name', 'Company');
        })->get();

        return $users;
    }

    public function getGroupUpdateData($user, $parent)
    {
        $type = Str::slug($user->type, '-');
        $updateData = [];
        if ($type) {
            switch ($type) {
                case 'super-admin':
                case 'vendor':
                    break;
                case 'company':
                    $updateData['vendor_id'] = @$parent->id;
                    break;
                case 'manager':
                    $updateData['vendor_id'] = @$parent->vendor_id;
                    $updateData['company_id'] = @$parent->id;
                    break;
                case 'staff':
                    $updateData['vendor_id'] = @$parent->vendor_id;
                    $updateData['company_id'] = @$parent->company_id;
                    $updateData['manager_id'] = @$parent->id;
                    break;
                case 'customer':
                    $updateData['vendor_id'] = @$parent->vendor_id;
                    $updateData['company_id'] = @$parent->id;
                    break;
                default: break;
            }
        }

        return @$updateData;
    }

    public function addUserToGroup($user, $parentUserId)
    {
        return null;
    }
}
