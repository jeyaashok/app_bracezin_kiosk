<?php

namespace Role\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Role\Repositories\RoleRepository;
use Tji;
use Validation;
use UserFcd;

class RoleController
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the records .
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();

        if (Tji::getRequestType($request) != 'api') {
            $input = Tji::getDefaultParamForWeb($input);
        }
        $input['with'] = 'permissions';
        $items = $this->roleRepository->all($input);

        return Tji::indexResponse($request, [
            'data' => $items,
            'webUrl' => 'role::index',
            'ajaxUrl' => 'role::index']);
    }

    public function indexDataTable(Request $request)
    {
        $input = Tji::getDefaultParamForDataTable($request);
        $items = $this->roleRepository->all($input);
        $input['viewLocation'] = 'role::list';
        $tableOutput = Tji::DataTableMapView($input, $items);

        return $tableOutput;
    }

    /**
     * Store a newly created Record in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            Validation::checkOn($input, ['name' => 'required|string|min:3|max:25|unique:roles,name']);
            $item = $this->roleRepository->store($input);
            DB::commit();
            if ($request->is('api/*')) {
                return response()->json(['data' => $item,
                    'code' => Response::HTTP_CREATED,
                    'response' => Response::$statusTexts[Response::HTTP_CREATED]], Response::HTTP_CREATED);
            } else {
                return redirect()->route('role.index')->withStatus(__('Records Successfully Created.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return response()->json(compact('error'), 405);
        }
    }

    /**
     * Update the specified record in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $item = $this->roleRepository->update($id, $request->all());
            DB::commit();
            if ($request->is('api/*')) {
                return response()->json(['data' => $item,
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
            } else {
                return redirect()->route('role.index')->withStatus(__('Records Successfully Updated.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return response()->json(compact('error'), 405);
        }
    }

    /**
     * Get a single record with the id
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $input = $request->all();
        $item = $this->roleRepository->findById($id, $input);

        if ($request->is('api/*')) {
            return response()->json(['data' => $item,
                'code' => Response::HTTP_OK,
                'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
        } else {
            return view('role::role.show', ['role' => $item]);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $item = $this->roleRepository->findById($id);
            if (! empty($item)) {
                $item->delete();
            }
            DB::commit();
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Deleted Successfully.',
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
            } else {
                return redirect()->route('role.index')->withStatus(__('Records Deleted.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return response()->json(compact('error'), 405);
        }
    }

    public function mapPermissionByRole(Request $request)
    {
        DB::beginTransaction();
        try {
            $item = $this->roleRepository->mapPermissionByRole($request->all());
            DB::commit();

            return Tji::showResponse($request, ['data' => $item]);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return Tji::errorResponse($request, $e);
        }
    }

    public function syncRolePermissions(Request $request)
    {
        $input = $request->all();
        $data = $input['data'] ?? [];
        DB::beginTransaction();
        try {
            if ($data && count($data) > 0) {
                foreach ($data as $key => $value) {
                    if (! isset($value['role_id']) || ! isset($value['permission_id'])) {
                        throw new \Exception('Role Id and Permission Id is required.');
                    } else {
                        $rolePermissionData = [
                            'role_id' => $value['role_id'],
                            'permission_id' => $value['permission_id'],
                        ];
                        $state = $value['state'] ?? false;
                        if ($state) {
                            $this->roleRepository->assignPermissionToRole($rolePermissionData);
                        } else {
                            $this->roleRepository->removePermissionFromRole($rolePermissionData);
                        }
                    }
                }
            }
            $items = $this->roleRepository->all(['with' => 'permissions']);
            DB::commit();

            return response()->json(['data' => $items,
                'code' => Response::HTTP_OK,
                'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();

            return Tji::errorResponse($request, $e);
        }
    }

    public function userSyncRole(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $state = $input['state'] ?? true;
            Validation::checkOn($input, ['user_id' => 'required|integer']);
            $roleName = $input['role_name'] ?? null;
            if (! $roleName) { Validation::checkOn($input, ['role_id' => 'required|integer']); }
            $user = UserFcd::getUser($input['user_id']);
            $roleName = ($roleName) ? $roleName : $this->roleRepository->findById($input['role_id'])->name;
            if($state) {
                $item = ($user && $user->id) ? $user->assignRole($roleName) : null;
            } else {
                $item = ($user && $user->id) ? $user->removeRole($roleName) : null;
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
