<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        //return $request->expectsJson() ? null : route('login');
        if (!$request->expectsJson()) {
            // For web requests (like from a browser)
            // return route('login'); // REMOVE or COMMENT THIS
            abort(401, 'Unauthorized');
        }

        // For API requests, don't redirect anywhere — return null
        return null;
    }
}
