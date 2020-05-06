<?php namespace Igniter\Api;

use Event;
use Igniter\Api\Exception\ExceptionHandler;
use System\Classes\BaseExtension;

/**
 * Api Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/api.php', 'api');

        $this->registerResponseFactory();

        $this->registerConsoleCommand('create.apiresource', \Igniter\Api\Console\CreateApiResource::class);

        $this->registerExceptionHandler();
    }

    public function boot()
    {
    }

    public function registerNavigation()
    {
        return [
            'tools' => [
                'child' => [
                    'resources' => [
                        'priority' => 2,
                        'class' => 'api-resources',
                        'href' => admin_url('igniter/api/resources'),
                        'title' => 'APIs',
                        'permission' => 'Igniter.Api',
                    ],
                ],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Igniter.Api.Manage' => [
                'description' => 'Create, modify and delete api resources',
                'group' => 'module',
            ],
        ];
    }

    public function registerApiResources()
    {
        return [
            'menus' => [
                'name' => 'Menus',
                'description' => 'An API resource for menus',
                'model' => \Admin\Models\Menus_model::class,
                'controller' => \Igniter\Api\ApiResources\Menus::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class,
            ],
            'categories' => [
                'name' => 'Categories',
                'description' => 'An API resource for categories',
                'model' => \Admin\Models\Categories_model::class,
                'controller' => \Igniter\Api\ApiResources\Categories::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\CategoryTransformer::class,
            ],
            'locations' => [
                'name' => 'Locations',
                'description' => 'An API resource for locations',
                'model' => \Admin\Models\Locations_model::class,
                'controller' => \Igniter\Api\ApiResources\Locations::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class,
            ],
        ];
    }
    
    /**
     * Register the response factory.
     *
     * @return void
     */
    protected function registerResponseFactory()
    {
        $this->app->alias('api.response', \Igniter\Api\Classes\ResponseFactory::class);

        $this->app->singleton('api.response', function ($app) {
            return new \Igniter\Api\Classes\ResponseFactory();
        });
    }

    protected function registerExceptionHandler()
    {
        Event::listen('exception.beforeRender', function ($exception, $httpCode, $request) {
            if (!$request->is('api/*'))
                return;

            $format = $this->app['config']->get('api.errorFormat');
            $handler = new ExceptionHandler($format);

            return $handler->handleException($exception);
        });
    }
}
