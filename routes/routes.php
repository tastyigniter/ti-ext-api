<?php

Route::middleware('api')
    ->as('igniter.api.')
    ->prefix(config('igniter.api.prefix'))
    ->group(function ($router) {
        $router->post('/token', [\Igniter\Api\Http\Controllers\Tokens::class, 'create']);
    });
