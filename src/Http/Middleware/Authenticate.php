<?php

declare(strict_types=1);

namespace Igniter\Api\Http\Middleware;

use Closure;
use Igniter\Api\Exceptions;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request):mixed $next
     * @param string ...$guards
     * @return mixed
     *
     * @throws \Igniter\Api\Exceptions\AuthenticationException
     */
    #[\Override]
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $guard = config('igniter.api.guard');

            if (!empty($guard)) {
                $guards[] = $guard;
            }

            return parent::handle($request, $next, ...$guards);
        } catch (AuthenticationException $e) {
            throw new Exceptions\AuthenticationException('Unauthenticated.', $e->guards());
        }
    }
}
