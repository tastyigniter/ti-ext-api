<?php

$api = app('api.router');

$api->version('v1', function ($api) {
    // Define the routes to issue tokens.
    $api->post('/token', [\Igniter\Api\Controllers\Tokens::class, 'create']);
});
