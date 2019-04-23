<?php

namespace Igniter\Api\Classes;

use File;
use Igniter\Flame\Traits\Singleton;

class ApiManager
{
    use Singleton;

    protected $resourcesPath;

    protected $baseUri = 'api';

    protected $namespace = '\\Igniter\\Api\\Resources';

    public function initialize()
    {
        $this->resourcesPath = storage_path('framework/api-resources.php');
    }

    public function getBaseEndpoint($endpoint = null)
    {
        $prefix = config('api.prefix');

        $base = $this->baseUri.($prefix ? '/'.$prefix : '');

        return is_null($endpoint) ? $base : $base.'/'.$endpoint;
    }

    public function getResources()
    {
        if (!File::isFile($this->resourcesPath))
            return [];

        return File::getRequire($this->resourcesPath);
    }

    public function buildResource($name, $model, $meta = [])
    {
        $controller = $this->parseName($name);
        $singularController = str_singular($controller);

        \Artisan::call('create:apiresource', [
            'extension' => 'Igniter.Api',
            'controller' => $controller,
            '--model' => $model,
            '--meta' => $meta,
        ]);

        if (!class_exists($controllerName = $this->namespace."\\{$controller}"))
            return [null, null];

        return [$controllerName, $this->namespace."\\Transformers\\{$singularController}Transformer"];
    }

    public function writeResources(array $resources)
    {
        $content = [];
        foreach ($resources as $endpoint => $resource) {
            $content[$endpoint] = [
                'controller' => array_get($resource, 'controller') ?: 'Igniter\Api\Classes\ApiController',
                'only' => array_get($resource, 'meta.actions', ['index', 'store', 'show', 'update', 'destroy']),
                'middleware' => array_get($resource, 'meta.middleware', ['api']),
                'prefix' => 'v2',
            ];
        }

        File::put($this->resourcesPath, '<?php return '.var_export($content, TRUE).';');
    }

    protected function parseName($name)
    {
        return studly_case(preg_replace('/[0-9]+/', '', $name));
    }

    protected function getClassPath($class)
    {
        $path = trim(str_replace('\\', '/', $class), '/');

        return extension_path(strtolower(dirname($path)).'/'.basename($path).'.php');
    }
}