<?php

namespace Igniter\Api\Auth;

use Dingo\Api\Contract\Auth\Provider;
use Dingo\Api\Routing\Route;
use Igniter\Api\Classes\ApiManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SanctumProvider implements Provider
{
    protected $authManager;

    public function __construct()
    {
        $this->authManager = Manager::instance();
    }

    public function authenticate(Request $request, Route $route)
    {
        $accessToken = $this->authManager->authenticate($request->bearerToken());

        $allowedGroup = $this->getAllowedGroup($route);
        if ($allowedGroup === 'all')
            return $accessToken;

        if ($allowedGroup !== 'guest' && !$accessToken)
            throw new UnauthorizedHttpException('Bearer', lang('igniter.api::default.alert_auth_failed'));

        if (!$this->authManager->checkGroup($allowedGroup, $accessToken))
            throw new AccessDeniedHttpException(lang('igniter.api::default.alert_auth_restricted'));

        return optional($accessToken)->tokenable;
    }

    protected function getAllowedGroup(Route $route)
    {
        $resourceOptions = optional(ApiManager::instance()->getCurrentResource())->options ?? [];

        $authActions = array_get($resourceOptions, 'authorization', []);

        return array_get($authActions, $route->getActionMethod(), 'admin');
    }
}
