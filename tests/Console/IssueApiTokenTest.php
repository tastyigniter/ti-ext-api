<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Console;

use Igniter\Api\Console\IssueApiToken;
use Igniter\Api\Models\Token;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;
use Mockery;

it('issues token for admin user with valid email', function(): void {
    User::factory()->superUser()->create(['email' => 'admin@example.com']);
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('admin@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(true);
    $command->shouldReceive('info')->once();

    $command->handle();
});

it('issues token for customer user with valid email', function(): void {
    Customer::factory()->create(['email' => 'customer@example.com']);
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('customer@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(false);
    $command->shouldReceive('info')->once();

    $command->handle();
});

it('returns error when user does not exist', function(): void {
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('nonexistent@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(true);

    $command->shouldReceive('error')->with('Admin user does not exist! Remove --admin option to issue token for customer.')->once();

    $command->handle();
});

function staffForConsoleToken(array $permissions): User
{
    $role = UserRole::factory()->create(['permissions' => array_fill_keys($permissions, 1)]);

    return User::factory()->for($role, 'role')->create(['email' => 'staff@example.com', 'super_user' => false]);
}

it('limits staff token abilities to the staff permissions', function(): void {
    $user = staffForConsoleToken(['Admin.Categories']);

    $this->artisan('igniter:api-token', [
        '--name' => 'Till',
        '--email' => 'staff@example.com',
        '--admin' => true,
        '--abilities' => ['*', 'orders:index'],
    ])->expectsOutputToContain('Abilities allowed by the staff permissions: categories:index')
        ->assertExitCode(0);

    expect(Token::query()->where('tokenable_id', $user->getKey())->sole()->abilities)
        ->toEqualCanonicalizing(['categories:index', 'categories:show', 'categories:store', 'categories:update', 'categories:destroy']);
});

it('does not issue a staff token when no requested ability is permitted', function(): void {
    $user = staffForConsoleToken(['Admin.Categories']);

    $this->artisan('igniter:api-token', [
        '--name' => 'Till',
        '--email' => 'staff@example.com',
        '--admin' => true,
        '--abilities' => ['orders:*'],
    ])->expectsOutput(lang('igniter.api::default.alert_token_abilities_not_permitted'));

    expect(Token::query()->where('tokenable_id', $user->getKey())->exists())->toBeFalse();
});
