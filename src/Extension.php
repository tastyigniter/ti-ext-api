<?php

declare(strict_types=1);

namespace Igniter\Api;

use Igniter\Api\ApiResources\Addresses;
use Igniter\Api\ApiResources\AppSettings;
use Igniter\Api\ApiResources\Categories;
use Igniter\Api\ApiResources\Currencies;
use Igniter\Api\ApiResources\Customers;
use Igniter\Api\ApiResources\DiningTables;
use Igniter\Api\ApiResources\Locations;
use Igniter\Api\ApiResources\LocationSettings;
use Igniter\Api\ApiResources\MenuItemOptions;
use Igniter\Api\ApiResources\MenuOptions;
use Igniter\Api\ApiResources\Menus;
use Igniter\Api\ApiResources\Notifications;
use Igniter\Api\ApiResources\Orders;
use Igniter\Api\ApiResources\Reservations;
use Igniter\Api\ApiResources\Reviews;
use Igniter\Api\ApiResources\Status;
use Igniter\Api\ApiResources\Stocks;
use Igniter\Api\ApiResources\Users;
use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Classes\LoginCodeManager;
use Igniter\Api\Console\IssueApiToken;
use Igniter\Api\Exceptions\ErrorHandler;
use Igniter\Api\Listeners\AccountSubscriber;
use Igniter\Api\Listeners\TokenEventSubscriber;
use Igniter\Api\Models\Token;
use Igniter\System\Classes\BaseExtension;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\SanctumServiceProvider;
use Override;
use Spatie\Fractal\FractalServiceProvider;

/**
 * Api Extension Information File
 */
class Extension extends BaseExtension
{
    public array $singletons = [
        LoginCodeManager::class,
    ];

    protected $subscribe = [
        TokenEventSubscriber::class,
        AccountSubscriber::class,
    ];

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/api.php', 'igniter-api');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/api.php' => config_path('igniter-api.php')], 'igniter-config');
        }

        Sanctum::usePersonalAccessTokenModel(Token::class);

        $this->app->register(FractalServiceProvider::class);
        $this->app->register(SanctumServiceProvider::class);

        $this->app['config']->set('fractal.default_serializer', $this->app['config']->get('igniter-api.serializer'));

        $this->app->singleton(ApiManager::class);

        $this->registerErrorHandler();

        $this->registerConsoleCommand('api.token', IssueApiToken::class);
    }

    #[Override]
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Register all the available API routes
        ApiManager::registerRoutes();

        $this->sanctumConfigureAuth();
        $this->sanctumConfigureMiddleware();
    }

    #[Override]
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
                        'permission' => 'Igniter.Api.*',
                    ],
                ],
            ],
        ];
    }

    #[Override]
    public function registerPermissions(): array
    {
        return [
            'Igniter.Api.Manage' => [
                'description' => 'Create, modify and delete api resources',
                'group' => 'igniter::system.permissions.name',
            ],
        ];
    }

    public function registerApiResources(): array
    {
        return [
            'categories' => [
                'controller' => Categories::class,
                'name' => 'Categories',
                'description' => 'An API resource for categories',
                'actions' => [
                    'index', 'show:all', 'store:admin:admin', 'update:admin:admin', 'destroy:admin:admin',
                ],
            ],
            'currencies' => [
                'controller' => Currencies::class,
                'name' => 'Currencies',
                'description' => 'An API resource for currencies',
                'actions' => [
                    'index',
                ],
            ],
            'addresses' => [
                'controller' => Addresses::class,
                'name' => 'Addresses',
                'description' => 'An API resource for customer addresses',
                'actions' => [
                    'index:users:users', 'show:users:users',
                    'store:users:users', 'update:users:users',
                    'destroy:users:users',
                ],
            ],
            'customers' => [
                'controller' => Customers::class,
                'name' => 'Customers',
                'description' => 'An API resource for customers',
                'actions' => [
                    'index:admin:admin', 'show:admin:admin,customer',
                    'store:admin:admin', 'update:users:users',
                    'destroy:admin:admin',
                ],
            ],
            'locations' => [
                'controller' => Locations::class,
                'name' => 'Locations',
                'description' => 'An API resource for locations',
                'actions' => [
                    'index:all', 'show:admin',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'location_settings' => [
                'controller' => LocationSettings::class,
                'name' => 'LocationSettings',
                'description' => 'An API resource for location settings',
                'actions' => [
                    'index:admin:admin', 'show:admin:admin',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'menus' => [
                'controller' => Menus::class,
                'name' => 'Menus',
                'description' => 'An API resource for menus',
                'actions' => [
                    'index:all', 'show:all',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'stocks' => [
                'controller' => Stocks::class,
                'name' => 'Stocks',
                'description' => 'An API resource for stock levels',
                'actions' => [
                    'index:admin:admin', 'show:admin:admin',
                    'store:admin:admin', 'update:admin:admin',
                ],
            ],
            'notifications' => [
                'controller' => Notifications::class,
                'name' => 'Notifications',
                'description' => 'An API resource for staff notifications',
                'actions' => [
                    'index:admin:admin', 'show:admin:admin',
                    'update:admin:admin', 'destroy:admin:admin',
                ],
            ],
            'menu_options' => [
                'controller' => MenuOptions::class,
                'name' => 'MenuOptions',
                'description' => 'An API resource for Menu options',
                'actions' => [
                    'index:all', 'show:all',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'menu_item_options' => [
                'controller' => MenuItemOptions::class,
                'name' => 'MenuItemOptions',
                'description' => 'An API resource for Menu item options',
                'actions' => [
                    'index:all', 'show:all',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'orders' => [
                'controller' => Orders::class,
                'name' => 'Orders',
                'description' => 'An API resource for orders',
                'actions' => [
                    'index:users:users', 'show:users:users',
                    'store:users:users', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'reservations' => [
                'controller' => Reservations::class,
                'name' => 'Reservations',
                'description' => 'An API resource for reservations',
                'actions' => [
                    'index:users:users', 'show:users:users',
                    'store:users:users', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'reviews' => [
                'controller' => Reviews::class,
                'name' => 'Reviews',
                'description' => 'An API resource for reviews',
                'actions' => [
                    'index:users', 'show:users',
                    'store:users:users', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'tables' => [
                'controller' => DiningTables::class,
                'name' => 'Tables',
                'description' => 'An API resource for dining tables',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'status' => [
                'controller' => Status::class,
                'name' => 'Status',
                'description' => 'An API resource for status',
                'actions' => [
                    'index:admin', 'show:admin',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'users' => [
                'controller' => Users::class,
                'name' => 'Staff',
                'description' => 'An API resource for staff',
                'actions' => [
                    'index:admin:admin', 'show:admin:admin',
                    'store:admin:admin', 'update:admin:admin',
                    'destroy:admin:admin',
                ],
            ],
            'app_settings' => [
                'controller' => AppSettings::class,
                'name' => 'App Settings',
                'description' => 'App settings for the OrderPoint app',
                'actions' => [
                    'index:admin:admin',
                ],
            ],
        ];
    }

    protected function registerErrorHandler()
    {
        $this->callAfterResolving(ExceptionHandler::class, function($handler): void {
            new ErrorHandler($handler, config('igniter-api.errorFormat', []), config('igniter-api.debug', []));
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
        Sanctum::authenticateAccessTokensUsing(
            (new TokenEventSubscriber)->accessTokenIsValid(...),
        );
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip()));
    }
}
