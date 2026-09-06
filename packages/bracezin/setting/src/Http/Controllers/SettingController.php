<?php

namespace Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Setting\Http\Controllers\PackageController;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use DB;

class SettingController extends PackageController {

	public function getRepository(){
		return $this->settingRepository;
	}
	
}
