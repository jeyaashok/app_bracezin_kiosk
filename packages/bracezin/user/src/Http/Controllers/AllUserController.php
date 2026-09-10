<?php

namespace User\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Tji;

class AllUserController extends PackageController
{
    public function getRepository()
    {
        return $this->allUserRepository;
    }

    public function storeDataInit($input)
    {
        $input['username'] = @$input['username'] ?: @$input['email'] ?: @$input['mobile'] ?: @$input['name'];
        $input['type'] = @$input['type'] ?: Config('userConfig.default_user_type', 'customer');
        $input['password'] = $input['password'] ?: Config('userConfig.default_user_password', 'P@ssw0rd');
        if (Arr::get($input, 'email')) {
            Validation::checkOn($input, ['email' => 'required|string|email|min:4|max:50|unique:users,email']);
        }
        if (Arr::get($input, 'mobile')) {
            Validation::checkOn($input, ['mobile' => 'required|string|min:8|max:12|unique:users,mobile']);
        }
        if (Arr::get($input, 'username')) {
            Validation::checkOn($input, ['username' => 'required|string|min:4|max:50|unique:users,username']);
        }

        return $input;
    }

    public function updateResponseInit($item, $input)
    {
        $item = $this->repository->findById($item->id, ['with' => 'detail']);

        return $item;
    }

    public function mapPermissionByUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $item = $this->repository->mapPermissionByUser($request->all());
            $item['userPermissions'] = $this->getUserPermissions($item);
            DB::commit();

            return Tji::showResponse($request, ['data' => $item]);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return Tji::errorResponse($request, $e);
        }
    }

    public function getUserPermissions($user)
    {
        $userPermissions = [];
        if ($user && $user->tableName === 'users') {
            $allPermissions = ($user && $user->id) ? $user->getAllPermissions()->pluck('name') : [];
            $rolePermissions = ($user && $user->id) ? $user->getPermissionsViaRoles()->pluck('name') : [];
            $directPermissions = ($user && $user->id) ? $user->getDirectPermissions()->pluck('name') : [];
            $userPermissions['allPermissions'] = $allPermissions;
            $userPermissions['rolePermissions'] = $rolePermissions;
            $userPermissions['directPermissions'] = $directPermissions;
        }

        return $userPermissions;
    }
}
