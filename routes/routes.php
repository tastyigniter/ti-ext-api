<?php

Route::middleware('api')
    ->as('igniter.api.token.create')
    ->prefix(config('igniter.api.prefix'))
    ->group(function ($router) {
        $router->post('/token', [\Igniter\Api\Http\Controllers\Tokens::class, 'create']);
    });
