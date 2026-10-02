<?php

use Igniter\Api\ApiResources\Orders;
use Igniter\Api\ApiResources\Reservations;
use Igniter\Api\Http\Controllers\CreateToken;
use Igniter\Api\Http\Controllers\RevokeToken;
use Igniter\Api\Http\Controllers\ShowTokenUser;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->as('igniter.api.token.create')
    ->prefix(config('igniter-api.prefix'))
    ->group(function($router) {
        $router->post('/token', CreateToken::class);
    });

Route::middleware(config('igniter-api.middleware'))
    ->prefix(config('igniter-api.prefix'))
    ->group(function($router) {
        $router->get('/token/user', ShowTokenUser::class)->name('igniter.api.token.user');
        $router->delete('/token', RevokeToken::class)->name('igniter.api.token.revoke');

        $router->post('orders/totals', [Orders::class, 'totals'])
            ->name('igniter.api.orders.totals');
        $router->post('orders/{orderId}/accept', [Orders::class, 'accept'])
            ->name('igniter.api.orders.accept');
        $router->post('orders/{orderId}/reject', [Orders::class, 'reject'])
            ->name('igniter.api.orders.reject');
        $router->patch('orders/{orderId}/status', [Orders::class, 'updateStatus'])
            ->name('igniter.api.orders.update_status');
        $router->patch('reservations/{reservationId}/status', [Reservations::class, 'updateStatus'])
            ->name('igniter.api.reservations.update_status');
    });
