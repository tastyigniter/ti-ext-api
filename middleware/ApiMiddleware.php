<?php namespace Igniter\Api\Middleware;

use Auth;

class ApiMiddleware
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
