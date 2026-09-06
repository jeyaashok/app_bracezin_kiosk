<?php

namespace Tji\Http\Middleware;

use Closure;
use Config;

class forceSSL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $env = Config::get('app.env');

        if (!$request->secure() && ($env === 'live' || $env === 'production' || $env === 'stage')) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
