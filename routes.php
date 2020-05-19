<?php

use Igniter\Api\Models\ApiUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
            array_except($options, ['controller'])
        );
    }
});

// Define the Sanctum routes to initialize CSRF protection.
Route::group(['prefix' => config('sanctum.prefix', 'sanctum')], function () {
    Route::get(
        '/csrf-cookie',
        CsrfCookieController::class.'@show'
    )->middleware('web');
});

Route::post($apiManager->getBaseEndpoint().'/token', function (Request $request) {

    $request->validate([
        'username' => 'required',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = ApiCustomers::where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;

});

Route::post($apiManager->getBaseEndpoint().'/token/admin', function (Request $request) {

    $request->validate([
        'username' => 'required',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = ApiUsers::where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;

});