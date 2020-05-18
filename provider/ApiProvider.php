<?php namespace Igniter\Api\Provider;

use Main\Facades\Auth;
use Admin\Facades\AdminAuth;
use Laravel\Sanctum\SanctumServiceProvider;
use Igniter\Api\Middleware\ApiMiddleware;
use Illuminate\Contracts\Http\Kernel;

class ApiProvider extends SanctumServiceProvider
{
    protected function configureGuard()
    {
        Auth::resolved(function ($auth) {
            var_dump($auth);
            exit();
            $auth->viaRequest('sanctum', new Guard($auth, config('sanctum.expiration')));
        });
    }
    
    /**
     * Configure the Sanctum middleware and priority.
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->prependToMiddlewarePriority(ApiMiddleware::class);
    }    
}
