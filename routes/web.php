<?php

Route::middleware('api')
    ->as('igniter.api.token.create')
    ->prefix(config('igniter-api.prefix'))
    ->group(function($router) {
        $router->post('/token', \Igniter\Api\Http\Controllers\CreateToken::class);
    });

Route::middleware(config('igniter-api.middleware'))
    ->as('igniter.api.token.user')
    ->prefix(config('igniter-api.prefix'))
    ->group(function($router) {
        $router->get('/token/user', \Igniter\Api\Http\Controllers\ShowTokenUser::class);
    });
