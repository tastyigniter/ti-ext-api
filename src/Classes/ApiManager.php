<?php

declare(strict_types=1);

namespace Igniter\Api\Classes;

use Igniter\Api\Models\Resource;
use Igniter\Flame\Support\Facades\Igniter;
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
     *
     * @return array<string, string>
     */
    public function listTokenAbilities(): array
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

                foreach ($prefixes as $prefix) {
                    $abilities[$prefix.':'.$action] = sprintf('%s: %s', $resourceName, Resource::actionLabel($action));
                }
            }
        }

        return $abilities;
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
