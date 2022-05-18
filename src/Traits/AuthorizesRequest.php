<?php

namespace Igniter\Api\Traits;

use Dingo\Api\Auth\Auth;
use Dingo\Api\Dispatcher;
use Igniter\Api\Auth\Manager;

trait AuthorizesRequest
{
    /**
     * Controller scopes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Controller authentication providers.
     *
     * @var array
     */
    protected $authenticationProviders = [];

    /**
     * Controller rate limit and expiration.
     *
     * @var array
     */
    protected $rateLimit = [];

    /**
     * Controller rate limit throttles.
     *
     * @var array
     */
    protected $throttles = [];

    /**
     * Get the controllers rate limiting throttles.
     *
     * @return array
     */
    public function getThrottles()
    {
        return $this->throttles;
    }

    /**
     * Get the controllers rate limit and expiration.
     *
     * @return array
     */
    public function getRateLimit()
    {
        return $this->rateLimit;
    }

    /**
     * Get the controllers scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Get the controllers authentication providers.
     *
     * @return array
     */
    public function getAuthenticationProviders()
    {
        return $this->authenticationProviders;
    }

    /**
     * Get the internal dispatcher instance.
     *
     * @return \Dingo\Api\Dispatcher
     */
    public function api()
    {
        return app(Dispatcher::class);
    }

    /**
     * Get the authenticated token.
     *
     * @return mixed
     */
    protected function token()
    {
        return Manager::instance()->token();
    }

    /**
     * Get the authenticated user.
     *
     * @return mixed
     */
    protected function user()
    {
        return app(Auth::class)->user();
    }

    /**
     * Get the auth instance.
     *
     * @return \Dingo\Api\Auth\Auth
     */
    protected function auth()
    {
        return app(Auth::class);
    }

    protected function getToken()
    {
        return $this->token();
    }

    protected function checkToken($allowedGroup)
    {
        return Manager::instance()->checkGroup($allowedGroup, $this->token());
    }

    protected function tokenCan($abilities = ['*'])
    {
        return $this->token()->can($abilities);
    }
}
