<?php

namespace Igniter\Api\Classes;

use File;
use Igniter\Flame\Traits\Singleton;

class ApiManager
{
    use Singleton;

    protected $resourcesPath;

    protected $namespace = '\\Igniter\\Api\\Rest';

    public function initialize()
    {
        $this->resourcesPath = storage_path('framework/api-resources.php');
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

    public function deleteResource($controller)
    {
        $controllerPath = $this->getClassPath($controller);

        $transformer = str_singular(basename($controllerPath, '.php'));
        $transformerPath = $this->getClassPath($transformer);

        if (File::isFile($controllerPath))
            unlink($controllerPath);

        if (File::isFile($transformerPath))
            unlink($transformerPath);
    }

    public function writeResources(array $resources)
    {
        $content = [];
        foreach ($resources as $endpoint => $resource) {
            $content[$endpoint] = [
                'controller' => array_get($resource, 'controller') ?: 'Igniter\Api\Classes\ApiController',
                'only' => array_get($resource, 'meta.actions'),
                'middleware' => array_get($resource, 'meta.middleware', ['api'])
            ];
        }

        File::put($this->resourcesPath, '<?php return '.var_export($content, TRUE).';');
    }

    public function getTransformer($transformer)
    {
        $path = $this->getClassPath($transformer);
        if (!File::isFile($path))
            return null;

        return File::get($path);
    }

    public function writeTransformer($name, $content, $transformer = null)
    {
        if (empty($content))
            return null;

        $name = str_singular($this->parseName($name));

        if (!strlen($transformer))
            $transformer = $this->namespace."\\Transformers\\{$name}Transformer";

        $transformerPath = $this->getClassPath($transformer);
        $directory = dirname($transformerPath);

        if (!File::isDirectory($directory))
            File::makeDirectory($directory, 0777, TRUE);

        File::put($transformerPath, $content);

        return $transformer;
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