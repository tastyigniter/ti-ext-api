<?php
	
use AdminAuth;
use Laravel\Sanctum\Sanctum;
use Admin\Models\Users_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

$apiManager = \Igniter\Api\Classes\ApiManager::instance();

Route::group([
    'prefix' => $apiManager->getBaseEndpoint(),
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'api'],
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

Route::get($apiManager->getBaseEndpoint().'/token', function (Request $request) {
	
    $request->validate([
        'username' => 'required',
        'password' => 'required',
        'device_name' => 'required'
    ]);

    $user = Users_model::where('username', $request->username)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
    
});