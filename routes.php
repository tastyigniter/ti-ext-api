<?php

$api = app('api.router');

$api->version('v1', function ($api) {
    // Define the routes to issue tokens & initialize CSRF protection.
    $api->post('/token', [\Igniter\Api\Controllers\Tokens::class, 'create']);

    $api->get(
        '/csrf-cookie',
        \Laravel\Sanctum\Http\Controllers\CsrfCookieController::class.'@show'
    )->middleware('web');
});