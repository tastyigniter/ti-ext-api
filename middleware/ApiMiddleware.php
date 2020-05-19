<?php namespace Igniter\Api\Middleware;

use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiMiddleware
{
    /**
     * The authentication factory implementation.
     *
     * @var \Igniter\Flame\Auth\Manager
     */
    protected $auth;

    /**
     * The number of minutes tokens should be allowed to remain valid.
     *
     * @var int
     */
    protected $expiration;

    public function handle(Request $request, \Closure $next)
    {
        $this->auth = app(app()->runningInAdmin() ? 'admin.auth' : 'auth');
        $this->expiration = config('sanctum.expiration');

        if (!$this->authenticateToken($request->bearerToken()))
            throw new BadRequestHttpException;

        return $next($request);
    }

    protected function authenticateToken($token)
    {
        if ($user = $this->auth->user()) {
            return $this->supportsTokens($user)
                ? $user->withAccessToken(new TransientToken)
                : $user;
        }

        if ($token) {
            $model = Sanctum::$personalAccessTokenModel;

            $accessToken = $model::findToken($token);

            if (!$accessToken ||
                ($this->expiration &&
                    $accessToken->created_at->lte(now()->subMinutes($this->expiration)))) {
                return FALSE;
            }

            $user = $accessToken->tokenable;

            return $this->supportsTokens($user) ? $user->withAccessToken(
                tap($accessToken->forceFill(['last_used_at' => now()]))->save()
            ) : null;
        }
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        return $tokenable AND ($tokenable instanceof \Igniter\Api\Models\ApiUsers OR $tokenable instanceof \Igniter\Api\Models\ApiCustomers);
    }
}