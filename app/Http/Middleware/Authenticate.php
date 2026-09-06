<?php

namespace App\Http\Middleware;

use App\Http\Exceptions\ErrorResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->is('api/*')) {
            throw new ErrorResponse('UnAuthorized Access, Token Not Valid', 401, 'info');
        }

        parent::unauthenticated($request, $guards);
    }
}
