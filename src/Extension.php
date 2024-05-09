<?php

namespace Igniter\Api;

use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Classes\Fractal;
use Igniter\Api\Exceptions\ErrorHandler;
use Igniter\Api\Listeners\TokenEventSubscriber;
use Igniter\System\Classes\BaseExtension;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\SanctumServiceProvider;
use Spatie\Fractal\FractalServiceProvider;

/**
 * Api Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/api.php', 'igniter.api');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/api.php' => config_path('igniter-api.php')], 'igniter-config');
        }

        Sanctum::ignoreMigrations();
        Sanctum::usePersonalAccessTokenModel(Models\Token::class);

        $this->app->register(FractalServiceProvider::class);
        $this->app->register(SanctumServiceProvider::class);

        $this->app['config']->set('fractal.fractal_class', Fractal::class);
        $this->app['config']->set('fractal.default_serializer', $this->app['config']->get('igniter.api.serializer'));

        $this->app->singleton(ApiManager::class);

        $this->registerErrorHandler();

        $this->registerConsoleCommand('api.token', Console\IssueApiToken::class);
    }

    public function boot()
    {
        $this->configureRateLimiting();

        // Register all the available API routes
        Classes\ApiManager::registerRoutes();

        $this->sanctumConfigureAuth();
        $this->sanctumConfigureMiddleware();
    }

    public function registerNavigation(): array
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

    public function registerPermissions(): array
    {
        return [
            'Igniter.Api.Manage' => [
                'description' => 'Create, modify and delete api resources',
                'group' => 'igniter::system.permissions.name',
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
                'actions' => [
                    'index', 'show:all', 'store:admin', 'update:admin', 'destroy:admin',
                ],
            ],
            'currencies' => [
                'controller' => \Igniter\Api\ApiResources\Currencies::class,
                'name' => 'Currencies',
                'description' => 'An API resource for currencies',
                'actions' => [
                    'index',
                ],
            ],
            'customers' => [
                'controller' => \Igniter\Api\ApiResources\Customers::class,
                'name' => 'Customers',
                'description' => 'An API resource for customers',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin', 'update:users',
                    'destroy:admin',
                ],
            ],
            'locations' => [
                'controller' => \Igniter\Api\ApiResources\Locations::class,
                'name' => 'Locations',
                'description' => 'An API resource for locations',
                'actions' => [
                    'index:all', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'menus' => [
                'controller' => \Igniter\Api\ApiResources\Menus::class,
                'name' => 'Menus',
                'description' => 'An API resource for menus',
                'actions' => [
                    'index:all', 'show:all',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'menu_options' => [
                'controller' => \Igniter\Api\ApiResources\MenuOptions::class,
                'name' => 'MenuOptions',
                'description' => 'An API resource for Menu options',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'menu_item_options' => [
                'controller' => \Igniter\Api\ApiResources\MenuItemOptions::class,
                'name' => 'MenuItemOptions',
                'description' => 'An API resource for Menu item options',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'orders' => [
                'controller' => \Igniter\Api\ApiResources\Orders::class,
                'name' => 'Orders',
                'description' => 'An API resource for orders',
                'actions' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'reservations' => [
                'controller' => \Igniter\Api\ApiResources\Reservations::class,
                'name' => 'Reservations',
                'description' => 'An API resource for reservations',
                'actions' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'reviews' => [
                'controller' => \Igniter\Api\ApiResources\Reviews::class,
                'name' => 'Reviews',
                'description' => 'An API resource for reviews',
                'actions' => [
                    'index:users', 'show:users',
                    'store:users', 'update:admin',
                    'destroy:admin',
                ],
            ],
            'tables' => [
                'controller' => \Igniter\Api\ApiResources\DiningTables::class,
                'name' => 'Tables',
                'description' => 'An API resource for dining tables',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin', 'update:admin',
                    'destroy:admin',
                ],
            ],
        ];
    }

    protected function registerErrorHandler()
    {
        $this->callAfterResolving(ExceptionHandler::class, function ($handler) {
            new ErrorHandler($handler, config('igniter.api.errorFormat', []), config('igniter.api.debug', []));
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

    protected function sanctumConfigureAuth()
    {
        Event::subscribe(TokenEventSubscriber::class);
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
