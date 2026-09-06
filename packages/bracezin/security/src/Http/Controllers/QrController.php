<?php

namespace Security\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Security\Http\Controllers\PackageController;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use DB;
use Qr;

class QrController extends PackageController {

    public function getRepository() {
        return $this->qrRepository;
    }

    public function readQrcode(Request $request) {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $resource = Qr::readQrcode($input);
            DB::commit();
            return Tji::successResponse($request, $resource);
        } catch (Exception $e) {
            DB::rollBack();
            return Tji::errorResponse($request, $e);
        }
    }
}
   
