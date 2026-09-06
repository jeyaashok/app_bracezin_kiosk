<?php

namespace Layout\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Layout\Http\Controllers\PackageController;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use DB;

class MenuGroupController extends PackageController {

	public function getRepository(){
		return $this->menuGroupRepository;
	}
	public function storeDataInit($input) {
		$name = @$input['title'];
		$input['slug'] = Str::slug($input['title'], '-');
		return $input;
	}

	// public function updateDataInit($id, $input) {
	// 	$name = @$input['title'];
	// 	$input['slug'] = Str::slug($input['title'], '-');


	// 	return $input;
	// }
}
