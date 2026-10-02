<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Api\Models\Token;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Http\Request;

it('revokes the current access token', function(): void {
    $user = User::factory()->superUser()->create();
    $accessToken = Token::createToken($user, 'mobile', ['*']);

    expect(Token::query()->whereKey($accessToken->accessToken->getKey())->exists())->toBeTrue();

    $this->withToken($accessToken->plainTextToken)
        ->delete(route('igniter.api.token.revoke'))
        ->assertNoContent();

    expect(Token::query()->whereKey($accessToken->accessToken->getKey())->exists())->toBeFalse();
});

it('revokes customer tokens', function(): void {
    $customer = Customer::factory()->create(['is_activated' => true]);
    $accessToken = Token::createToken($customer, 'mobile', ['orders:*']);

    $this->withToken($accessToken->plainTextToken)
        ->delete(route('igniter.api.token.revoke'))
        ->assertNoContent();

    expect(Token::query()->whereKey($accessToken->accessToken->getKey())->exists())->toBeFalse();
});

it('rejects unauthenticated revoke requests', function(): void {
    $this->delete(route('igniter.api.token.revoke'))
        ->assertUnauthorized();
});

it('returns json 401 when invoke receives no user', function(): void {
    $response = (new \Igniter\Api\Http\Controllers\RevokeToken)(Request::create('/', 'DELETE'));

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getData(true))->toMatchArray([
            'status_code' => 401,
            'message' => 'Unauthenticated.',
        ]);
});
