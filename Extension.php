<?php

namespace Igniter\Api;

use Admin\Models\Customers_model;
use Admin\Models\Users_model;
use Igniter\Flame\Database\Model;
use Illuminate\Contracts\Http\Kernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;
use System\Classes\BaseExtension;

/**
 * Api Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/api.php', 'api');
        $this->mergeConfigFrom(__DIR__.'/config/sanctum.php', 'sanctum');

        Sanctum::usePersonalAccessTokenModel(Models\Token::class);

        $this->app->register(\Dingo\Api\Provider\LaravelServiceProvider::class);

        $this->registerResponseFactory();
        $this->registerSerializer();

        $this->registerConsoleCommand('create.apiresource', Console\CreateApiResource::class);
        $this->registerConsoleCommand('api.token', Console\IssueApiToken::class);
    }

    public function boot()
    {
        // Register all the available API routes
        Classes\ApiManager::instance();

        $this->sanctumConfigureAuthModels();
        $this->sanctumConfigureMiddleware();
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
            'categories' => [
                'controller' => \Igniter\Api\ApiResources\Categories::class,
                'name' => 'Categories',
                'description' => 'An API resource for categories',
                'authorization' => [
                    'index:all', 'show:all',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'customers' => [
                'controller' => \Igniter\Api\ApiResources\Customers::class,
                'name' => 'Customers',
                'description' => 'An API resource for customers',
                'authorization' => [
                    'index:admin', 'show:admin',
                    'store:users', 'update:users',
                    'destroy:admin',
                ],
            ],
            'locations' => [
                'controller' => \Igniter\Api\ApiResources\Locations::class,
                'name' => 'Locations',
                'description' => 'An API resource for locations',
                'authorization' => [
                    'index:all', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'menus' => [
                'controller' => \Igniter\Api\ApiResources\Menus::class,
                'name' => 'Menus',
                'description' => 'An API resource for menus',
                'authorization' => [
                    'index:all', 'show:all',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'orders' => [
                'controller' => \Igniter\Api\ApiResources\Orders::class,
                'name' => 'Orders',
                'description' => 'An API resource for orders',
                'authorization' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'reservations' => [
                'controller' => \Igniter\Api\ApiResources\Reservations::class,
                'name' => 'Reservations',
                'description' => 'An API resource for reservations',
                'authorization' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'reviews' => [
                'controller' => \Igniter\Api\ApiResources\Reviews::class,
                'name' => 'Reviews',
                'description' => 'An API resource for reviews',
                'authorization' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'tables' => [
                'controller' => \Igniter\Api\ApiResources\Tables::class,
                'name' => 'Tables',
                'description' => 'An API resource for tables',
                'authorization' => [
                    'index:admin', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
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
        $this->app->alias('api.response', Classes\ResponseFactory::class);

        $this->app->singleton('api.response', function ($app) {
            return new Classes\ResponseFactory(
                $app['api.http.response'],
                $app['api.transformer']
            );
        });
    }

    /**
     * Configure the Sanctum middleware and priority.
     *
     * @return void
     */
    protected function sanctumConfigureMiddleware()
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->prependToMiddlewarePriority(EnsureFrontendRequestsAreStateful::class);
    }

    protected function sanctumConfigureAuthModels()
    {
        Users_model::extend(function (Model $model) {
            $model->relation['morphMany']['tokens'] = [Sanctum::$personalAccessTokenModel, 'name' => 'tokenable', 'delete' => TRUE];
        });

        Customers_model::extend(function (Model $model) {
            $model->relation['morphMany']['tokens'] = [Sanctum::$personalAccessTokenModel, 'name' => 'tokenable', 'delete' => TRUE];
        });
    }

    protected function registerSerializer()
    {
        $this->app->resolving(\Dingo\Api\Transformer\Adapter\Fractal::class, function ($adapter, $app) {
            $serializer = config('api.serializer');
            $adapter->getFractal()->setSerializer(new $serializer);
        });
    }
}
