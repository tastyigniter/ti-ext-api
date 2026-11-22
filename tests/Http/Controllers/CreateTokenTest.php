<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Api\Http\Controllers\CreateToken;
use Igniter\Api\Models\Token;
use Igniter\Flame\Database\Model;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

beforeEach(function(): void {
    $this->controller = new CreateToken;
});

it('creates token for valid credentials', function(bool $isAdmin, Model $model): void {
    $this->post(route('igniter.api.token.create'), [
        'email' => $model->email,
        'password' => 'password',
        'is_admin' => $isAdmin,
        'device_name' => 'device',
        'abilities' => ['*'],
    ]);

    expect(Token::where('tokenable_type', $model->getMorphClass())
        ->where('tokenable_id', $model->getKey())
        ->exists())->toBeTrue();
})->with([
    [true, fn() => User::factory()->superUser()->create()],
    [false, fn() => Customer::factory()->create([
        'is_activated' => true,
    ])],
]);

it('throws validation exception for invalid admin credentials', function(bool $isAdmin, Model $model): void {
    $response = $this->post(route('igniter.api.token.create'), [
        'email' => $model->email,
        'password' => 'wrongpassword',
        'is_admin' => $isAdmin,
        'device_name' => 'device',
        'abilities' => ['*'],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email')
        ->assertJsonFragment(['The provided credentials are incorrect.']);
})->with([
    [true, fn() => User::factory()->superUser()->create()],
    [false, fn() => Customer::factory()->create([
        'is_activated' => true,
    ])],
]);

it('throws validation exception for inactive user', function(bool $isAdmin, Model $model): void {
    $response = $this->post(route('igniter.api.token.create'), [
        'email' => $model->email,
        'password' => 'password',
        'is_admin' => $isAdmin,
        'device_name' => 'device',
        'abilities' => ['*'],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email')
        ->assertJsonFragment(['Inactive user account']);
})->with([
    [true, fn() => User::factory()->superUser()->create([
        'is_activated' => false,
    ])],
    [false, fn() => Customer::factory()->create([
        'is_activated' => false,
    ])],
]);

it('rejects staff abilities for customer tokens', function(): void {
    $customer = Customer::factory()->create(['is_activated' => true]);

    $this->post(route('igniter.api.token.create'), [
        'email' => $customer->email,
        'password' => 'password',
        'is_admin' => false,
        'device_name' => 'exploit',
        'abilities' => ['staff:*'],
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('abilities');

    expect(Token::where('tokenable_type', $customer->getMorphClass())
        ->where('tokenable_id', $customer->getKey())
        ->exists())->toBeFalse();
});

it('issues customer tokens without wildcard staff abilities', function(): void {
    $customer = Customer::factory()->create(['is_activated' => true]);

    $this->post(route('igniter.api.token.create'), [
        'email' => $customer->email,
        'password' => 'password',
        'is_admin' => false,
        'device_name' => 'device',
        'abilities' => ['*'],
    ])->assertCreated();

    $token = Token::query()
        ->where('tokenable_type', $customer->getMorphClass())
        ->where('tokenable_id', $customer->getKey())
        ->first();

    expect($token->abilities)->not->toContain('*')
        ->and($token->abilities)->not->toContain('staff:*')
        ->and($token->abilities)->toBe(Token::customerAbilities());
});

it('issues customer tokens with abilities extended by an event', function(): void {
    Event::listen('api.token.extendCustomerAbilities', function(array &$abilities): void {
        $abilities[] = 'loyalty:*';
    });

    try {
        $customer = Customer::factory()->create(['is_activated' => true]);

        $this->post(route('igniter.api.token.create'), [
            'email' => $customer->email,
            'password' => 'password',
            'is_admin' => false,
            'device_name' => 'device',
            'abilities' => ['loyalty:*'],
        ])->assertCreated();

        $token = Token::query()
            ->where('tokenable_type', $customer->getMorphClass())
            ->where('tokenable_id', $customer->getKey())
            ->first();

        expect($token->abilities)->toBe(['loyalty:*']);
    } finally {
        Event::forget('api.token.extendCustomerAbilities');
    }
});

function staffForTokenRequest(array $permissions): User
{
    $role = UserRole::factory()->create(['permissions' => array_fill_keys($permissions, 1)]);

    return User::factory()->for($role, 'role')->create([
        'super_user' => false,
        'is_activated' => true,
        'status' => true,
        'password' => 'password',
    ]);
}

it('limits staff token abilities to the staff permissions', function(): void {
    $user = staffForTokenRequest(['Admin.Categories']);

    $this->postJson(route('igniter.api.token.create'), [
        'email' => $user->email,
        'password' => 'password',
        'is_admin' => true,
        'device_name' => 'Till',
        'abilities' => ['*', 'orders:index'],
    ])->assertCreated();

    expect(Token::query()->where('tokenable_id', $user->getKey())->sole()->abilities)
        ->toEqualCanonicalizing(['categories:index', 'categories:show', 'categories:store', 'categories:update', 'categories:destroy']);
});

it('rejects a staff token request when no requested ability is permitted', function(): void {
    $user = staffForTokenRequest(['Admin.Categories']);

    $this->postJson(route('igniter.api.token.create'), [
        'email' => $user->email,
        'password' => 'password',
        'is_admin' => true,
        'device_name' => 'Till',
        'abilities' => ['orders:*'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['abilities' => lang('igniter.api::default.alert_token_abilities_not_permitted')]);

    expect(Token::query()->where('tokenable_id', $user->getKey())->exists())->toBeFalse();
});

it('lets a beforeCreateToken listener refuse the token', function(): void {
    $user = User::factory()->superUser()->create();
    Event::listen('igniter.api.beforeCreateToken', function($tokenUser, $request) use ($user): void {
        expect($tokenUser->getKey())->toBe($user->getKey())
            ->and($request->input('two_factor_code'))->toBe('123456');

        throw ValidationException::withMessages(['two_factor_code' => 'Two factor code required.']);
    });

    $this->post(route('igniter.api.token.create'), [
        'email' => $user->email,
        'password' => 'password',
        'is_admin' => true,
        'device_name' => 'device',
        'two_factor_code' => '123456',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('two_factor_code');

    expect(Token::where('tokenable_id', $user->getKey())->where('tokenable_type', $user->getMorphClass())->exists())
        ->toBeFalse();
});
