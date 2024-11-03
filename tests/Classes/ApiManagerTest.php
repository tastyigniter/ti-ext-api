<?php

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\ApiManager;
use Illuminate\Support\Facades\Route;

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
