<?php

namespace Igniter\Api\Classes;

use Igniter\Api\Models\Resource;
use Igniter\Flame\Traits\Singleton;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApiManager
{
    use Singleton;

    protected $resources;

    /**
     * @var \Dingo\Api\Routing\Router
     */
    protected $router;

    /**
     * The access token the user is using for the current request.
     *
     * @var \Laravel\Sanctum\Contracts\HasAbilities
     */
    protected $accessToken;

    public function initialize()
    {
        $this->router = app('api.router');

        $this->registerRoutes();
    }

    public function getResources()
    {
        if (is_null($this->resources))
            $this->loadResources();

        return $this->resources;
    }

    public function getResource($endpoint)
    {
        return array_get($this->getResources(), $endpoint, []);
    }

    public function getCurrentResource()
    {
        $currentResourceName = Str::before(Str::after($this->router->currentRouteName(), 'api.'), '.');

        return $this->getResource($currentResourceName);
    }

    public function getCurrentAction()
    {
        return Str::afterLast($this->router->currentRouteAction(), '@');
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

        if (!class_exists($controllerName = $namespace."\\{$controller}"))
            return [null, null];

        return [$controllerName, $namespace."\\Transformers\\{$singularController}Transformer"];
    }

    protected function loadResources()
    {
        $resources = Resource::all()
            ->filter(function ($resource) {
                return class_exists($resource->controller);
            })
            ->filter(function ($resource) {
                return resolve($resource->controller)->isClassExtendedWith('Igniter.Api.Actions.RestController');
            })
            ->filter(function ($resource) {
                return count($resource->getAvailableActions()) > 0;
            })
            ->mapWithKeys(function ($resource) {
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

    protected function registerRoutes()
    {
        if (!app()->hasDatabase() || !Schema::hasTable('igniter_api_resources'))
            return;

        if (!$resources = $this->getResources())
            return;

        $this->router->version('v1', function ($api) use ($resources) {
            $api->group([
                'as' => 'api',
                'middleware' => ['api', 'api.auth'],
            ], function ($api) use ($resources) {
                foreach ($resources as $endpoint => $resourceObj) {
                    $api->resource(
                        $endpoint,
                        $resourceObj->controller,
                        $resourceObj->options
                    );
                }
            });
        });
    }

    protected function parseName($name)
    {
        return studly_case(preg_replace('/[0-9]+/', '', $name));
    }
}
