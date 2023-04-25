<?php

namespace Igniter\Api\Classes;

use Igniter\Api\Models\Resource;
use Igniter\Flame\Igniter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApiManager
{
    protected $resources;

    /**
     * The access token the user is using for the current request.
     *
     * @var \Laravel\Sanctum\Contracts\HasAbilities
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

    public function buildResource($name, $model, $meta = [])
    {
        $controller = $this->parseName($name);
        $singularController = str_singular($controller);
        $namespace = '\\Igniter\\Api\\ApiResources';

        Artisan::call('create:apiresource', [
            'extension' => 'Igniter.Api',
            'controller' => $controller,
            '--model' => $model,
            '--meta' => $meta,
        ]);

        if (!class_exists($controllerName = $namespace."\\{$controller}")) {
            return [null, null];
        }

        return [$controllerName, $namespace."\\Transformers\\{$singularController}Transformer"];
    }

    protected function loadResources()
    {
        $resources = Resource::all()->mapWithKeys(function ($resource) {
            $resourceObj = (object)[
                'endpoint' => $resource->endpoint,
                'controller' => $resource->controller,
                'options' => array_merge($resource->meta, [
                    'only' => $resource->getAvailableActions(),
                ]),
            ];

            return [$resource->endpoint => $resourceObj];
        });

        $this->resources = $resources;
    }

    public static function registerRoutes()
    {
        if (!Igniter::hasDatabase() || !Schema::hasTable('igniter_api_resources')) {
            return;
        }

        Route::middleware(config('igniter.api.middleware'))
            ->as('igniter.api.')
            ->prefix(config('igniter.api.prefix'))
            ->group(function ($router) {
                foreach (resolve(static::class)->getResources() as $endpoint => $resourceObj) {
                    if (!class_exists($resourceObj->controller)) {
                        continue;
                    }

                    $router->resource($endpoint, $resourceObj->controller, $resourceObj->options);
                }
            });
    }

    protected function parseName($name)
    {
        return studly_case(preg_replace('/[0-9]+/', '', $name));
    }
}
