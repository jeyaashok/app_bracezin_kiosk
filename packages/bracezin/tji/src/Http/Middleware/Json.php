<?php

namespace Tji\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Sys;

class Json
{
    protected $factory;

    public function __construct(ResponseFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($request, Closure $next)
    {
        // First, set the header so any other middleware knows we're
        // dealing with a should-be JSON response. 
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Get the response
        if($response->headers->get('content-type') == 'application/json')
        {
            $collection = Collect($response->original);
            $authUser = Sys::authUser();
            $collection->put('authUserId', @$authUser->id);
            $collection->put('authUserType', @$authUser->userTypeName);
            $response->setContent($collection);
        }

        return $response;
    }
}
