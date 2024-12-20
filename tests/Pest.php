<?php

use Igniter\User\Models\User;

uses(SamPoyigi\Testbench\TestCase::class)->in(__DIR__);

function callProtectedMethod(object $condition, string $methodName, array $args = []): mixed
{
    $reflection = new ReflectionClass($condition);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);
    return $method->invokeArgs($condition, $args);
}

function actingAsSuperUser()
{
    return test()->actingAs(User::factory()->superUser()->create(), 'igniter-admin');
}
