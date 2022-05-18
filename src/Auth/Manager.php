<?php

namespace Igniter\Api\Auth;

use Igniter\Flame\Traits\Singleton;
use Illuminate\Support\Facades\Request as RequestFacade;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\TransientToken;

class Manager
{
    use Singleton;

    /**
     * The access token the user is using for the current request.
     *
     * @var \Laravel\Sanctum\Contracts\HasAbilities
     */
    protected $token;

    public function check()
    {
        if (!is_null($this->token))
            return $this->token;

        return $this->authenticate(RequestFacade::bearerToken());
    }

    public function authenticate($token)
    {
        if (($user = app('auth')->user()) && $user->is_activated && $this->supportsTokens($user)) {
            $this->setToken($token = (new TransientToken));

            return $token;
        }

        if ($token) {
            if (!$token = $this->findToken($token))
                return null;

            $expiration = config('sanctum.expiration');
            if ($expiration && $token->created_at->lte(now()->subMinutes($expiration)))
                return null;

            $user = $token->tokenable;

            if (!$this->supportsTokens($user))
                return null;

            if (!$user->is_activated)
                return null;

            $this->setToken(
                tap($token->forceFill(['last_used_at' => now()]), function ($token) {
                    $token->save();
                })
            );

            return $token;
        }
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Laravel\Sanctum\Contracts\HasAbilities
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     * @return bool
     */
    public function can($ability)
    {
        return $this->token()->can($ability);
    }

    public function checkGroup(string $group, $token)
    {
        if (!is_null($token)) {
            if ($group == 'guest')
                return false;

            if ($group == 'admin' && !$token->isForAdmin())
                return false;

            if ($group == 'customer' && $token->isForAdmin())
                return false;
        }
        else {
            if (in_array($group, ['admin', 'customer', 'users']))
                return false;
        }

        return true;
    }

    /**
     * Determine if the current API token has admin access
     *
     * @return bool
     */
    public function tokenIsAdmin()
    {
        return optional($this->token())->isForAdmin();
    }

    /**
     * Set the current access token for the user.
     *
     * @param \Laravel\Sanctum\Contracts\HasAbilities $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param string $token
     * @return \Laravel\Sanctum\PersonalAccessToken
     */
    public function findToken($token)
    {
        $model = Sanctum::$personalAccessTokenModel;

        return $model::findToken($token);
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        if (is_null($tokenable))
            return false;

        if (in_array(HasApiTokens::class, class_uses_recursive(get_class($tokenable))))
            return true;

        return $tokenable instanceof \Igniter\Flame\Auth\Models\User && $tokenable->hasRelation('tokens');
    }
}
