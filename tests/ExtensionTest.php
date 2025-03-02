<?php

namespace Igniter\Api\Tests;

use Igniter\Api\Classes\Fractal;
use Igniter\Api\Models\Resource;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;

it('loads registered api resources', function() {
    $resources = Resource::listRegisteredResources();

    expect($resources)->toHaveKey('categories');
});

it('replaces fractal.fractal_class config item', function() {
    expect(config('fractal.fractal_class'))->toBe(Fractal::class);
});

it('creates access token using http', function($email, $isAdmin, $deviceName, $abilities) {
    $attributes = [
        'email' => $email,
        'status' => 1,
        'is_activated' => 1,
    ];

    $isAdmin
        ? User::factory()->create($attributes)
        : Customer::factory()->create($attributes);

    $response = $this->postJson('/api/token', [
        'email' => $email,
        'password' => 'password',
        'is_admin' => $isAdmin,
        'device_name' => $deviceName,
        'abilities' => $abilities,
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['status_code', 'token']);
})->with([
    ['admin@domain.tld', true, 'Test Device', ['*']],
    ['customer@domain.tld', false, 'Test Device', ['*']],
]);

it('creates access token using console command', function($email, $isAdmin, $deviceName, $abilities) {
    $attributes = [
        'email' => $email,
        'status' => 1,
        'is_activated' => 1,
    ];

    $isAdmin
        ? User::factory()->create($attributes)
        : Customer::factory()->create($attributes);

    $this->artisan('api:token', [
        '--name' => $deviceName,
        '--email' => $email,
        '--admin' => $isAdmin,
        '--abilities' => $abilities,
    ])->assertExitCode(0);
})->with([
    ['admin@domain.tld', true, 'Test Device', ['*']],
    ['customer@domain.tld', false, 'Test Device', ['*']],
]);

