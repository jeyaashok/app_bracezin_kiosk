<?php

namespace Tji\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tji\Http\Controllers\PackageController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Arr;
use Str;
use Sys;
use Tji;

class SysController extends PackageController {

	public function websocketsStart() {
		Tji::closeResponse();
		$onWebsocket = env('ENABLE_LOCAL_WEBSOCKET');
        if(!(Sys::checkWebsocketPort() && $onWebsocket && ($onWebsocket === true || $onWebsocket === 'true'))) {
			\Artisan::call('websockets:serve');
		}
	}

	public function websocketsRestart() {
		Tji::closeResponse();
		$onWebsocket = env('ENABLE_LOCAL_WEBSOCKET');
        if(Sys::checkWebsocketPort() && $onWebsocket && ($onWebsocket === true || $onWebsocket === 'true')) {
			\Artisan::call('websockets:restart');
		}
	    sleep(1);
	    $onWebsocket = env('ENABLE_LOCAL_WEBSOCKET');
        if(!(Sys::checkWebsocketPort() && $onWebsocket && ($onWebsocket === true || $onWebsocket === 'true'))) {
	    	\Artisan::call('websockets:serve');
	    }
	}

}
