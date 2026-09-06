<?php

namespace Tji\Http\Controllers;

use Carbon\Carbon;
use Arr;
use Str;
use Storage;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tji\Http\Controllers\PackageController;

class DiagnosticController extends PackageController {
	
	public function clearDatas(Request $request) {

		if($request->params == 'config-cache'){
			\Artisan::call('config:cache');
		}
		elseif($request->params == 'optimize-clear'){
			\Artisan::call('optimize:clear');
		}
		elseif($request->params == 'view-clear'){
			\Artisan::call('view:clear');
		 }
		elseif($request->params == 'cache-clear'){
			\Artisan::call('cache:clear');
		 }
		elseif($request->params == 'config-clear'){
			\Artisan::call('config:clear');
		}
		elseif($request->params == 'route-clear'){
			\Artisan::call('route:clear');
		}
		elseif($request->params == 'session-clear'){
			\Session::flush();
		}
		elseif($request->params == 'model-cache-clear'){
			\Artisan::call('modelCache:clear');
			\Artisan::call('optimize:clear');
		}
		elseif($request->params == 'auth-clear'){
			// \Artisan::call('auth:clear');			
		}
		elseif($request->params == 'database-memory-clear'){
			if(Storage::disk('public')->exists('json/database.json')) {
				Storage::disk('public')->delete('json/database.json');
			}
			\Artisan::call('modelCache:clear');
			\Artisan::call('optimize:clear');
		}
		elseif($request->params == 'permission-memory-clear'){
			if(Storage::disk('public')->exists('json/permissions.json')) {
				Storage::disk('public')->delete('json/permissions.json');
			}
		}
		elseif($request->params == 'all-log-clear'){
			if(Storage::disk('app')->exists('logs')) {
				Storage::disk('app')->delete('logs');
			}
		}
		elseif($request->params == 'syserror-log-clear'){
			if(Storage::disk('app')->exists('logs/sysError')) {
				Storage::disk('app')->delete('logs/sysError');
			}
		}

		return response()->json(['done'], 200);
	}

}
