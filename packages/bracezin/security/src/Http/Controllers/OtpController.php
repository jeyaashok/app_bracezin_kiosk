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

class OtpController extends PackageController {

    public function getRepository() {
        return $this->otpRepository;
    }
}
   
