<?php

namespace Igniter\Api\Listeners;

use Igniter\Api\Classes\ApiManager;
use Illuminate\Routing\Route;
use Laravel\Sanctum\Events\TokenAuthenticated;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TokenEventSubscriber
{
    public function handleTokenAuthenticated($event)
    {
        $accessToken = $event->token;

        $allowedGroup = $this->getAllowedGroup(request()->route());
        if ($allowedGroup === 'all') {
            return $accessToken;
        }

        if ($allowedGroup !== 'guest' && !$accessToken) {
            throw new UnauthorizedHttpException('Bearer', lang('igniter.api::default.alert_auth_failed'));
        }

        if (!$this->checkGroup($allowedGroup, $accessToken)) {
            throw new AccessDeniedHttpException(lang('igniter.api::default.alert_auth_restricted'));
        }

        return optional($accessToken)->tokenable;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            TokenAuthenticated::class,
            [$this, 'handleTokenAuthenticated'],
        );
    }

    protected function checkGroup(mixed $group, mixed $token)
    {
        if ($group == 'guest') {
            return false;
        }

        if ($group == 'admin' && !$token->isForAdmin()) {
            return false;
        }

        if ($group == 'customer' && $token->isForAdmin()) {
            return false;
        }

        return true;
    }

    protected function getAllowedGroup(Route $route)
    {
        $resourceOptions = optional(resolve(ApiManager::class)->getCurrentResource())->options ?? [];

        $authActions = array_get($resourceOptions, 'authorization', []);

        return array_get($authActions, $route->getActionMethod(), 'admin');
    }
}
