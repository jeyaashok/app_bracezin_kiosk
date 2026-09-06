<?php

namespace Role\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Role\Repositories\PermissionRepository;
use Role\Http\Controllers\PackageController;
use Arr;
use Str;
use DB;
use Validation;
use UserFcd;
use Tji;

class PermissionController extends PackageController{

	public function getRepository() {
		return $this->permissionRepository;
	}
	

	/**
	 * Store a newly created Record in storage.
	 *
	 * @param $request
	 *
	 * @return Response
	 */
	public function store(Request $request) {

		DB::beginTransaction();
		try {
			$input = $request->all();
			Validation::checkOn($input, ['name' => 'required|string|min:3|max:25|unique:permissions,name']);
			$permissions = @$input['permissions'] ?: [];
			if(!($permissions && is_array($permissions) && count($permissions) > 0 )) {
				$permissionName = @$input['name'] ?: null;
				$permissions = ($permissionName) ? [$permissionName] : $permissions;
			}
			if($permissions && is_array($permissions) && count($permissions) > 0 ) {
				foreach($permissions as $permission) {
					$input['name'] = $permission;
					$item = $this->repository->store($input);
				}
			}
			DB::commit();
			if($request->is('api/*')) {
				return response()->json(['data' => $item,
				                        'code' => Response::HTTP_CREATED,
				                        'response' => Response::$statusTexts[Response::HTTP_CREATED]], Response::HTTP_CREATED);
			} else {
				return redirect()->route('role.index')->withStatus(__('Records Successfully Created.'));
			}
		} catch(\Exception $e) {
			DB::rollBack();
			$error = $e->getMessage();
			return response()->json(compact('error'), 405);
		}

	}

	/**
	 * A permission can be applied to roles.
	 */
	public function roles(): BelongsToMany {
		return $this->belongsToMany(
			config('permission.models.role'),
			config('permission.table_names.role_has_permissions'),
			'permission_id',
			'role_id'
		);
	}

	/**
	 * A permission belongs to some users of the model associated with its guard.
	 */
	public function users(): MorphToMany {
		return $this->morphedByMany(
			getModelForGuard($this->attributes['guard_name']),
			'model',
			config('permission.table_names.model_has_permissions'),
			'permission_id',
			config('permission.column_names.model_morph_key')
		);
	}

    public function userSyncPermission(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $state = $input['state'] ?? true;
            Validation::checkOn($input, ['user_id' => 'required|integer']);
            $permissionName = $input['permission_name'] ?? null;
            if (! $permissionName) { Validation::checkOn($input, ['permission_id' => 'required|integer']); }
            $user = UserFcd::getUser($input['user_id']);
            $permissionName = ($permissionName) ? $permissionName : $this->permissionRepository->findById($input['permission_id'])->name;
            if($state) {
                $item = ($user && $user->id) ? $user->givePermissionTo($permissionName) : null;
            } else {
                $item = ($user && $user->id) ? $user->revokePermissionTo($permissionName) : null;
            }
            $user = UserFcd::GetFormattedData($input['user_id']);
            DB::commit();

            return Tji::showResponse($request, ['data' => $user]);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return Tji::errorResponse($request, $e);
        }
    }
}
