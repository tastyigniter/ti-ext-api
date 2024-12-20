<?php

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Tests\Fixtures\TestResource;
use Igniter\Flame\Igniter;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Mockery;

beforeEach(function() {
    $this->apiManager = new ApiManager();
});

it('returns resources when they are loaded', function() {
    expect($this->apiManager->getResources())->toHaveCount(12);
});

it('returns empty array when resource is not found', function() {
    $resource = $this->apiManager->getResource('non-existent-endpoint');

    expect($resource)->toBe([]);
});

it('returns current resource based on route name', function() {
    Route::shouldReceive('currentRouteName')->andReturn('api.categories.index');

    $currentResource = $this->apiManager->getCurrentResource();

    expect($currentResource->endpoint)->toBe('categories');
});

it('returns current action based on route action', function() {
    Route::shouldReceive('currentRouteAction')->andReturn('TestController@index');

    $currentAction = $this->apiManager->getCurrentAction();

    expect($currentAction)->toBe('index');
});

it('registers routes when database and table exist', function() {
    $router = Mockery::mock(Router::class);
    $routerRegister = Mockery::mock(RouteRegistrar::class);
    Route::shouldReceive('middleware')->andReturn($routerRegister);
    $routerRegister->shouldReceive('as')->andReturnSelf();
    $routerRegister->shouldReceive('prefix')->with('api')->andReturnSelf();
    $routerRegister->shouldReceive('group')->andReturnUsing(function($callback) use ($router) {
        $callback($router);

        return true;
    });
    $resourceObj = (object)[
        'controller' => TestResource::class,
        'options' => [],
    ];
    $apiManager = Mockery::mock(ApiManager::class)->makePartial();
    $apiManager->shouldReceive('getResources')->andReturn(['endpoint' => $resourceObj]);
    app()->instance(ApiManager::class, $apiManager);

    $router->shouldReceive('resource')->with('endpoint', TestResource::class, [])->once();

    ApiManager::registerRoutes();
});

it('does not register routes when database does not exist', function() {
    $igniter = new class extends Igniter
    {
        public static function clear()
        {
            static::$hasDatabase = null;
        }
    };
    $igniter::clear();
    $schema = $this->createMock(Builder::class);
    $connection = $this->createMock(Connection::class);
    $connection->expects($this->once())->method('getSchemaBuilder')->willReturn($schema);
    $schema->expects($this->once())->method('hasTable')->with('settings')->willReturn(false);
    app()->instance('db.connection', $connection);
    Schema::shouldReceive('hasTable')->with('igniter_api_resources')->never();
    Route::shouldReceive('middleware')->never();

    ApiManager::registerRoutes();
});

it('does not register routes when table does not exist', function() {
    $igniter = new class extends Igniter
    {
        public static function clear()
        {
            static::$hasDatabase = null;
        }
    };
    $igniter::clear();
    $schema = $this->createMock(Builder::class);
    $connection = $this->createMock(Connection::class);
    $connection->expects($this->once())->method('getSchemaBuilder')->willReturn($schema);
    $schema->expects($this->exactly(2))->method('hasTable')->willReturnMap([
        ['settings', true],
        ['extension_settings', true],
    ]);
    app()->instance('db.connection', $connection);
    Schema::shouldReceive('hasTable')->with('igniter_api_resources')->andReturn(false);
    Route::shouldReceive('middleware')->never();

    ApiManager::registerRoutes();
});

it('skips invalid controllers when registering routes', function() {
    $router = Mockery::mock(Router::class);
    $routerRegister = Mockery::mock(RouteRegistrar::class);
    Route::shouldReceive('middleware')->andReturn($routerRegister);
    $routerRegister->shouldReceive('as')->andReturnSelf();
    $routerRegister->shouldReceive('prefix')->with('api')->andReturnSelf();
    $routerRegister->shouldReceive('group')->andReturnUsing(function($callback) use ($router) {
        $callback($router);

        return true;
    });
    $resourceObj = (object)[
        'controller' => 'InvalidController',
        'options' => [],
    ];
    $apiManager = Mockery::mock(ApiManager::class)->makePartial();
    $apiManager->shouldReceive('getResources')->andReturn(['endpoint' => $resourceObj]);
    app()->instance(ApiManager::class, $apiManager);

    $router->shouldReceive('resource')->never();

    ApiManager::registerRoutes();
});
