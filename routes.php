<?php

use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

$apiManager = \Igniter\Api\Classes\ApiManager::instance();

Route::group([
    'prefix' => $apiManager->getBaseEndpoint(),
    'as' => 'api.',
    'middleware' => ['api'],
], function () use ($apiManager) {
    $resources = $apiManager->getResources();
    foreach ($resources as $name => $options) {
        Route::resource(
            $name,
            array_get($options, 'controller'),
            array_except($options, ['controller', 'authorization'])
        );
    }
});

// Define the routes to issue tokens & initialize CSRF protection.
Route::group([
    'prefix' => $apiManager->getBaseEndpoint(),
], function () {
    Route::post('/token', function (Request $request) {
        return [
            'status_code' => 201,
            'token' => \Igniter\Api\Classes\ApiManager::createToken($request),
        ];
    });

    Route::post('/admin/token', function (Request $request) {
        return [
            'status_code' => 201,
            'token' => \Igniter\Api\Classes\ApiManager::createToken($request, TRUE),
        ];
    });

    Route::get(
        '/csrf-cookie',
        CsrfCookieController::class.'@show'
    )->middleware('web');
});
