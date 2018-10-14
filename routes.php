<?php

Route::group([
    'prefix' => 'api',
    'as' => 'api.',
    'middleware' => ['api']
], function () {
    $resources = \Igniter\Api\Classes\ApiManager::instance()->getResources();
    foreach ($resources as $name => $options) {
        Route::resource(
            $name,
            array_get($options, 'controller'),
            array_except($options, ['controller'])
        );
    }
});