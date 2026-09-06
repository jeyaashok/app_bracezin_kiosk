<?php

namespace Tji\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Closure;
use Carbon\Carbon;
use Arr;
use Str;
use Gate;
use DB;
use Sys;
use Log;

class Init {
	
	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

        $authUser = Sys::authUser();
        if(is_null($authUser)) {
            $authUser = (auth('admin')->check()) ? auth('admin')->user() : null;
            Sys::setAuthUser($authUser);
        }
        
		if (is_null($authUser)) {
			return $next($request);
		}

		$sysAdmin = ($authUser && $authUser->is_sysAdmin) ? true : false;
		if ($sysAdmin) {
			return $next($request);
		} else {
			// $userType = ($authUser && $authUser->userTypeName) ? $authUser->userTypeName : null;
			return $next($request);
		}
	}
}
