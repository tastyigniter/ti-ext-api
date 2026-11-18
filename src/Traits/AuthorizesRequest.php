<?php

declare(strict_types=1);

namespace Igniter\Api\Traits;

use Igniter\User\Auth\Models\User as AuthUser;

trait AuthorizesRequest
{
    /**
     * Get the authenticated token.
     *
     * @return mixed
     */
    protected function token()
    {
        /** @var AuthUser $user */
        $user = request()->user();

        return $user->currentAccessToken();
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

    protected function auth()
    {
        return app('auth');
    }

    protected function getToken()
    {
        return $this->token();
    }

    protected function tokenCan($ability = '*')
    {
        /** @var AuthUser $user */
        $user = request()->user();

        return $user->tokenCan($ability);
    }
}
