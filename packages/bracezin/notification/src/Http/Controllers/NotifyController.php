<?php

namespace Notification\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Notification\Http\Controllers\PackageController;
use Carbon\Carbon;
use Storage;
use Tji;
use Arr;
use Str;
use DB;
  
class NotifyController extends PackageController {

	public function getRepository() {
		return $this->notifyRepository;
	}
}
