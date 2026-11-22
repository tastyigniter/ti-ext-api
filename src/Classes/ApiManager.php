<?php

declare(strict_types=1);

namespace Igniter\Api\Classes;

use Igniter\Api\Models\Resource;
use Igniter\Flame\Support\Facades\Igniter;
use Igniter\User\Models\User;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\Contracts\HasAbilities;
use ReflectionClass;

class ApiManager
{
    protected $resources;

    /**
     * The access token the user is using for the current request.
     *
     * @var HasAbilities
     */
    protected $accessToken;

    public function getResources()
    {
        if (is_null($this->resources)) {
            $this->loadResources();
        }

        return $this->resources;
    }

    public function getResource($endpoint)
    {
        return array_get($this->getResources(), $endpoint, []);
    }

    public function getCurrentResource()
    {
        $currentResourceName = Str::before(Str::after(Route::currentRouteName(), 'api.'), '.');

        return $this->getResource($currentResourceName);
    }

    public function getCurrentAction()
    {
        return Str::afterLast(Route::currentRouteAction(), '@');
    }

    /**
     * Abilities a staff token can be given: one per resource action that is enabled and
     * open to staff, such as `categories:index`, keyed by ability and labelled for display.
     * Given a user, only actions whose registered permission the user has are listed;
     * actions without a registered permission are left out.
     *
     * @return array<string, string>
     */
    public function listTokenAbilities(?User $user = null): array
    {
        $abilities = [];
        foreach (Resource::all()->sortBy('name') as $resource) {
            $resourceName = (string)($resource->name ?: $resource->endpoint);
            $controller = (string)$resource->controller;
            if (!class_exists($controller)) {
                continue;
            }

            $prefixes = array_unique(array_map(
                fn(string $ability): string => Str::before($ability, ':'),
                array_filter((array)((new ReflectionClass($controller))->getDefaultProperties()['requiredAbilities'] ?? []),
                    fn($ability): bool => is_string($ability) && $ability !== '' && $ability !== '*'),
            ));

            foreach ($resource->getAvailableActions() as $action) {
                // Customer-only actions can't be used with staff tokens.
                if ($resource->getActionAuthorization($action) === 'customer') {
                    continue;
                }

                if ($user instanceof User && !$this->userCanUseAction($user, $resource, $action)) {
                    continue;
                }

                foreach ($prefixes as $prefix) {
                    $abilities[$prefix.':'.$action] = sprintf('%s: %s', $resourceName, Resource::actionLabel($action));
                }
            }
        }

        return $abilities;
    }

    /**
     * Narrows requested abilities to those the staff user's permissions allow. A wildcard such
     * as `*` or `orders:*` expands to the permitted abilities it covers. Super users keep
     * what they request.
     *
     * @param list<string> $abilities
     * @return list<string>
     */
    public function limitTokenAbilities(User $user, array $abilities): array
    {
        if ($user->isSuperUser()) {
            return array_values($abilities);
        }

        $permitted = array_keys($this->listTokenAbilities($user));
        $limited = [];
        foreach ($abilities as $ability) {
            $limited = array_merge($limited, match (true) {
                $ability === '*' => $permitted,
                str_ends_with($ability, ':*') => array_filter($permitted, fn(string $permittedAbility): bool => Str::before($permittedAbility, ':') === Str::before($ability, ':')),
                default => in_array($ability, $permitted, true) ? [$ability] : [],
            });
        }

        return array_values(array_unique($limited));
    }

    /**
     * Whether the staff user may perform the action on the resource handling the
     * current request. Public actions (`guest` or `all`) are open to anyone, so
     * staff are not limited by permissions there. Other actions without a
     * registered permission are refused, as they are when token abilities are issued.
     */
    public function userCanUseCurrentAction(User $user, string $action): bool
    {
        $currentResource = $this->getCurrentResource();
        $endpoint = $currentResource->endpoint ?? null;
        if (!is_string($endpoint) || $endpoint === '') {
            return false;
        }

        $authorization = array_get((array)($currentResource->options['authorization'] ?? []), $action, 'all');
        if (in_array($authorization, ['guest', 'all'], true)) {
            return true;
        }

        $resource = new Resource;
        $resource->endpoint = $endpoint;

        return $this->userCanUseAction($user, $resource, $action);
    }

    protected function userCanUseAction(User $user, Resource $resource, string $action): bool
    {
        $permission = $resource->getActionPermission($action);

        return $permission !== null && $user->hasAnyPermission($permission);
    }

    protected function loadResources()
    {
        Resource::syncAll();

        $resources = Resource::all()->mapWithKeys(function(Resource $resource): array {
            $resourceObj = (object)[
                'endpoint' => $resource->endpoint,
                'controller' => $resource->controller,
                'options' => array_merge((array)$resource->meta, [
                    'only' => $resource->getAvailableActions(),
                    'authorization' => $resource->getAuthorizationMap(),
                ]),
            ];

            return [$resource->endpoint => $resourceObj];
        });

        $this->resources = $resources;
    }

    public static function registerRoutes(): void
    {
        if (!Igniter::hasDatabase() || !Schema::hasTable('igniter_api_resources')) {
            return;
        }

        Route::middleware(config('igniter-api.middleware'))
            ->as('igniter.api.')
            ->prefix(config('igniter-api.prefix'))
            ->group(function(Router $router): void {
                foreach (resolve(static::class)->getResources() as $endpoint => $resourceObj) {
                    if (!class_exists((string)$resourceObj->controller)) {
                        continue;
                    }

                    $router->resource($endpoint, $resourceObj->controller, $resourceObj->options);
                }
            });
    }
}
