<?php
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
            array_except($options, ['controller'])
        );
    }
});