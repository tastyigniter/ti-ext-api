<?php

namespace Igniter\Api;

use Admin\Models\Customers_model;
use Admin\Models\Users_model;
use Event;
use Igniter\Api\Exception\ExceptionHandler;
use Igniter\Api\Models\Token;
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

        Sanctum::usePersonalAccessTokenModel(Token::class);

        $this->registerResponseFactory();

        $this->registerConsoleCommand('create.apiresource', \Igniter\Api\Console\CreateApiResource::class);
        $this->registerConsoleCommand('issue.apitoken', \Igniter\Api\Console\IssueApiToken::class);

        $this->registerExceptionHandler();
    }

    public function boot()
    {
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
                'name' => 'Categories',
                'description' => 'An API resource for categories',
                'model' => \Admin\Models\Categories_model::class,
                'controller' => \Igniter\Api\ApiResources\Categories::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\CategoryTransformer::class,
            ],
            'customers' => [
                'name' => 'Customers',
                'description' => 'An API resource for customers',
                'model' => \Admin\Models\Customers_model::class,
                'controller' => \Igniter\Api\ApiResources\Customers::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\CustomerTransformer::class,
            ],
            'locations' => [
                'name' => 'Locations',
                'description' => 'An API resource for locations',
                'model' => \Admin\Models\Locations_model::class,
                'controller' => \Igniter\Api\ApiResources\Locations::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class,
            ],
            'menus' => [
                'name' => 'Menus',
                'description' => 'An API resource for menus',
                'model' => \Admin\Models\Menus_model::class,
                'controller' => \Igniter\Api\ApiResources\Menus::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class,
            ],
            'orders' => [
                'name' => 'Orders',
                'description' => 'An API resource for orders',
                'model' => \Admin\Models\Orders_model::class,
                'controller' => \Igniter\Api\ApiResources\Orders::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\OrderTransformer::class,
            ],
            'reservations' => [
                'name' => 'Reservations',
                'description' => 'An API resource for reservations',
                'model' => \Admin\Models\Reservations_model::class,
                'controller' => \Igniter\Api\ApiResources\Reservations::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\ReservationTransformer::class,
            ],
            'reviews' => [
                'name' => 'Reviews',
                'description' => 'An API resource for reviews',
                'model' => \Admin\Models\Reviews_model::class,
                'controller' => \Igniter\Api\ApiResources\Reviews::class,
                'transformer' => \Igniter\Api\ApiResources\Transformers\ReviewTransformer::class,
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
}
