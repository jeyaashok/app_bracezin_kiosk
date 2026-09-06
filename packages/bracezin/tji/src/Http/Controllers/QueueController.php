<?php

namespace Tji\Http\Controllers;

use Carbon\Carbon;
use Arr;
use Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tji\Http\Controllers\PackageController;

class QueueController extends PackageController {

	public function executeRegularJob() {
		\Artisan::call('queue:work --queue=high --stop-when-empty');
		return response()->json(['success'], 200);
	}

}
