<?php

namespace User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use User\Http\Controllers\PackageController;
use UserFcd;
use DB;

class UserController extends PackageController {

    private $authUser;

    /**
     * Display a listing of the records .
     * @param Request $request
     * @return Response
     */
    public function getRepository() {
        return $this->allUserRepository;
    }

    public function index(Request $request) {
        $input = $request->all();
        $items = UserFcd::getUserModel($input);
        return response()->json(compact('items'));
    }

    public function setAuthUser() {
        if(!($this->authUser && $this->authUser->id)) {
            $this->authUser = (auth('admin')->check()) ? auth('admin')->user() : null;
        }
        return;
    }

    public function show(Request $request, $id) {
        $item = UserFcd::getUser($id);
        if($request->is('api/*')) {
            return response()->json(['data' => $item,
                    'code' => Response::HTTP_OK, 
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
        } else {
            return view('user::index', ['user' => $item]);
        }
    }

    /**
     * Update the specified record in storage.
     * @param int        $id
     * @param $request
     * @return Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $item = UserFcd::updateUser($input,$id);
            DB::commit();
            if($request->is('api/*')) {
                return response()->json(['data' => $item,
                        'code' => Response::HTTP_OK, 
                        'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();
            return response()->json(compact('error'), 405);
        }
    }
}
