<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Models;

use Igniter\Api\Models\Token;
use Igniter\Flame\Database\Model;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\NewAccessToken;
use Mockery;

it('creates a new personal access token for the user', function(): void {
    $tokenable = Mockery::mock(Model::class)->makePartial();
    $tokenable->shouldReceive('tokens->create')->andReturn(new Token(['id' => 1, 'token' => 'hashedToken']));
    $name = 'testToken';
    $abilities = ['*'];

    $result = Token::createToken($tokenable, $name, $abilities);

    expect($result)->toBeInstanceOf(NewAccessToken::class);
});

it('determines if the token belongs to an admin', function(): void {
    $token = new Token;
    $token->tokenable_type = User::make()->getMorphClass();

    $result = $token->isForAdmin();

    expect($result)->toBeTrue();
});

it('determines if the token belongs to a customer', function(): void {
    $token = new Token;
    $token->tokenable_type = Customer::make()->getMorphClass();

    $result = $token->isForCustomer();

    expect($result)->toBeTrue();
});

it('configures token model correctly', function(): void {
    $token = new Token;

    expect($token->getTable())->toBe('igniter_api_access_tokens');
});

it('identifies customer scoped abilities', function(): void {
    expect(Token::isCustomerAbility('orders:*'))->toBeTrue()
        ->and(Token::isCustomerAbility('customers:read'))->toBeTrue()
        ->and(Token::isCustomerAbility('addresses:*'))->toBeTrue()
        ->and(Token::isCustomerAbility('staff:*'))->toBeFalse()
        ->and(Token::isCustomerAbility('*'))->toBeFalse();
});

it('sanitizes wildcard abilities for customers', function(): void {
    $customer = new Customer;

    expect(Token::sanitizeAbilities($customer, ['*']))->toBe(Token::customerAbilities())
        ->and(Token::sanitizeAbilities($customer, []))->toBe(Token::customerAbilities())
        ->and(Token::sanitizeAbilities($customer, ['orders:*', 'staff:*']))->toBe(['orders:*']);
});

it('does not restrict abilities for staff tokenables', function(): void {
    expect(Token::sanitizeAbilities(new User, ['staff:*']))->toBe(['staff:*'])
        ->and(Token::sanitizeAbilities(new User, []))->toBe(['*']);
});

it('allows extending customer abilities using an event', function(): void {
    Event::listen('api.token.extendCustomerAbilities', function(array &$abilities): void {
        $abilities[] = 'rewards:*';
    });
    Event::listen('api.token.extendCustomerAbilities', fn(): array => ['points:*', 'staff:*']);

    try {
        expect(Token::customerAbilities())->toContain('rewards:*')
            ->and(Token::customerAbilities())->toContain('points:*')
            ->and(Token::customerAbilities())->toContain('staff:*')
            ->and(Token::isCustomerAbility('rewards:*'))->toBeTrue()
            ->and(Token::sanitizeAbilities(new Customer, ['rewards:*']))->toBe(['rewards:*']);
    } finally {
        Event::forget('api.token.extendCustomerAbilities');
    }
});
