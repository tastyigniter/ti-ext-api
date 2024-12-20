<?php

namespace Igniter\Api\Tests\Console;

use Igniter\Api\Console\IssueApiToken;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Mockery;

it('issues token for admin user with valid email', function() {
    User::factory()->create(['email' => 'admin@example.com']);
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('admin@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(true);
    $command->shouldReceive('info')->once();

    $command->handle();
});

it('issues token for customer user with valid email', function() {
    Customer::factory()->create(['email' => 'customer@example.com']);
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('customer@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(false);
    $command->shouldReceive('info')->once();

    $command->handle();
});

it('returns error when user does not exist', function() {
    $command = Mockery::mock(IssueApiToken::class)->makePartial();
    $command->shouldReceive('option')->with('name')->andReturn('Test Token');
    $command->shouldReceive('option')->with('email')->andReturn('nonexistent@example.com');
    $command->shouldReceive('option')->with('abilities')->andReturn(['*']);
    $command->shouldReceive('option')->with('admin')->andReturn(true);

    $command->shouldReceive('error')->with('User does not exist!')->once();

    $command->handle();
});
