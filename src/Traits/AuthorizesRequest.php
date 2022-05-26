<?php

namespace Igniter\Api\Traits;

trait AuthorizesRequest
{
    /**
     * Get the authenticated token.
     *
     * @return mixed
     */
    protected function token()
    {
        return request()->user()->currentAccessToken();
    }

    /**
     * Get the authenticated user.
     *
     * @return mixed
     */
    protected function user()
    {
        return request()->user();
    }

    /**
     * Get the auth instance.
     *
     * @return \Dingo\Api\Auth\Auth
     */
    protected function auth()
    {
        return app('auth');
    }

    protected function getToken()
    {
        return $this->token();
    }

    protected function tokenCan($abilities = ['*'])
    {
        return request()->user()->tokenCan($abilities);
    }
}
